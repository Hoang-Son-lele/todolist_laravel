<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $projects = $user->projects()->latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAdmin();
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planning,active,completed,on_hold',
        ]);

        $validated['user_id'] = Auth::id();

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * Check if user is admin
     */
    private function authorizeAdmin()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Chỉ admin mới có thể tạo project');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        // Lọc task: Chỉ lấy những task thuộc về user đang đăng nhập
        $tasks = $project->tasks()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('projects.show', compact('project', 'tasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorizeAdmin();
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorizeAdmin();
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planning,active,completed,on_hold',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorizeAdmin();
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }


    /**
     * Hiển thị báo cáo Timeline (Gantt Chart) cho Admin
     */
    public function report(Project $project)
    {
        $this->authorizeAdmin();
        $tasks = $project->tasks()
            ->with('assignedTo')
            ->orderBy('start_date', 'asc')
            ->get();

        $projectStart = $project->start_date;


        foreach ($tasks as $task) {

            $totalDays = 90;
            $task->offset_percent = ($projectStart->diffInDays($task->start_date, false) / $totalDays) * 100;
            $task->duration_percent = ($task->start_date->diffInDays($task->end_date, false) / $totalDays) * 100;
        }

        return view('projects.report', compact('project', 'tasks'));
    }
}
