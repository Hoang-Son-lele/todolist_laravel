<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::with('user', 'assignedTo', 'project')->paginate(10);
        } else {
            $tasks = Task::where('assigned_to', $user->id)->with('user', 'assignedTo', 'project')->paginate(10);
        }

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $user = Auth::user();
        $users = User::where('role', '!=', 'admin')->get();
        $projects = $user->projects()->get();
        $projectId = request('project_id');
        return view('tasks.create', compact('users', 'projects', 'projectId'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'difficulty' => 'required|in:easy,medium,hard',
            'assigned_to' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        $redirectRoute = $validated['project_id']
            ? route('projects.show', $validated['project_id'])
            : route('tasks.index');

        return redirect($redirectRoute)->with('success', 'Task được tạo thành công');
    }

    public function edit(Task $task)
    {
        $user = Auth::user();

        // Admin có thể edit bất kỳ task nào
        // User chỉ có thể edit task được giao cho họ
        if ($user->role !== 'admin' && $task->assigned_to !== $user->id) {
            abort(403, 'Bạn không có quyền chỉnh sửa task này');
        }

        $users = User::where('role', '!=', 'admin')->get();
        $projects = $user->role === 'admin'
            ? Project::all()
            : $user->projects()->get();

        return view('tasks.edit', compact('task', 'users', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $task->assigned_to !== $user->id) {
            abort(403, 'Bạn không có quyền cập nhật task này');
        }

        // Validation khác nhau cho admin và user
        if ($user->role === 'admin') {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,completed',
                'difficulty' => 'required|in:easy,medium,hard',
                'assigned_to' => 'required|exists:users,id',
                'project_id' => 'nullable|exists:projects,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'completion_percentage' => 'nullable|integer|min:0|max:100',
            ]);
            $task->update($validated);
        } else {
            $validated = $request->validate([
                'description' => 'nullable|string',
                'status' => 'required|in:pending,in_progress,completed',
                // 'difficulty' => 'nullable|in:easy,medium,hard',
                // 'start_date' => 'nullable|date',
                // 'end_date' => 'nullable|date',
                'completion_percentage' => 'nullable|integer|min:0|max:100',
            ]);
            $task->update($validated);
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('tasks.index')->with('success', 'Cập nhật thành công');
        }


        $redirectRoute = $task->project_id
            ? route('projects.show', $task->project_id)
            : route('tasks.index');

        return redirect($redirectRoute)->with('success', 'Task được cập nhật thành công');
    }

    public function destroy(Task $task)
    {
        $this->authorizeAdmin();
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task được xóa thành công');
    }

    public function authorizeAdmin()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Chỉ admin mới có thể quản lý task');
        }
    }
}
