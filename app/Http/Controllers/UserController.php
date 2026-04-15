<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Hiển thị dashboard với danh sách tasks của user
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tasks = Task::where('assigned_to', $user->id)
            ->with('project', 'user')
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Task::where('assigned_to', $user->id)->count(),
            'pending' => Task::where('assigned_to', $user->id)->where('status', 'pending')->count(),
            'in_progress' => Task::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'completed' => Task::where('assigned_to', $user->id)->where('status', 'completed')->count(),
        ];

        return view('user.dashboard', compact('tasks', 'stats'));
    }


    public function updateTaskStatus(Request $request, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);

            \Log::info('Task found', [
                'task_id' => $task->id,
                'assigned_to' => $task->assigned_to,
                'auth_id' => Auth::id(),
            ]);

            if ($task->assigned_to != Auth::id()) {
                \Log::warning('Authorization failed', [
                    'task_assigned_to' => $task->assigned_to,
                    'auth_id' => Auth::id(),
                ]);
                abort(403, 'Unauthorized');
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
                'completion_percentage' => 'nullable|integer|min:0|max:100',
            ]);

            $task->update($validated);

            return redirect()->back()->with('success', 'Trạng thái và phần trăm task đã được cập nhật!');
        } catch (\Throwable $e) {
            \Log::error('Error in updateTaskStatus', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }


    public function updateTaskStatusAjax(Request $request, Task $task)
    {

        if ($task->assigned_to != Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái task đã được cập nhật!',
            'task' => $task->load('project'),
        ]);
    }


    public function viewTask($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->assigned_to != Auth::id()) {
            abort(403, 'Bạn không có quyền xem task này!');
        }

        return view('user.task-detail', compact('task'));
    }


    public function updateTask(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật task này!');
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            // 'difficulty' => 'nullable|in:easy,medium,hard',
            // 'start_date' => 'nullable|date',
            // 'end_date' => 'nullable|date',
            'completion_percentage' => 'required|integer|min:0|max:100',
        ]);

        $task->update(array_filter($validated));

        return redirect()->back()->with('success', 'Task đã được cập nhật!');
    }

    public function updateTaskCompletion(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->assigned_to != Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền cập nhật task này!');
        }

        $validated = $request->validate([
            'completion_percentage' => 'required|integer|min:0|max:100',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'Phần trăm hoàn thành đã được cập nhật!');
    }
}
