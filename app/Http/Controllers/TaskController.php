<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::with('user', 'assignedTo')->paginate(10);
        } else {
            $tasks = Task::where('assigned_to', $user->id)->with('user', 'assignedTo')->paginate(10);
        }

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $users = User::where('role', '!=', 'admin')->get();
        return view('tasks.create', compact('users'));
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
        ]);

        Task::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task được tạo thành công');
    }

    public function edit(Task $task)
    {
        $this->authorizeAdmin();
        $users = User::where('role', '!=', 'admin')->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'difficulty' => 'required|in:easy,medium,hard',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task được cập nhật thành công');
    }

    public function destroy(Task $task)
    {
        $this->authorizeAdmin();
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task được xóa thành công');
    }

    private function authorizeAdmin()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Chỉ admin mới có thể quản lý task');
        }
    }
}
