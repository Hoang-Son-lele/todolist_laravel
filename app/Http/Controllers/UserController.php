<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

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

    /**
     * Gửi thông báo cho quản lý về task hết hạn
     */
    public function sendDeadlineEmailToManager(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->assigned_to != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này!'
            ], 403);
        }

        try {
            // Validate message
            $validated = $request->validate([
                'custom_message' => 'required|string|min:5|max:1000',
            ]);

            $manager = $task->user;

            if (!$manager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quản lý của task!'
                ], 404);
            }

            // Gửi thông báo cho quản lý với message tùy chỉnh
            $manager->notify(new \App\Notifications\TaskDeadlineWarning($task, $validated['custom_message']));

            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được gửi cho quản lý!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending deadline notification', [
                'message' => $e->getMessage(),
                'task_id' => $taskId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông báo của user hiện tại
     */
    public function getNotifications()
    {
        $notifications = Auth::user()
            ->notifications()
            ->where('type', 'App\Notifications\TaskDeadlineWarning')
            ->latest()
            ->paginate(10);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Đánh dấu thông báo là đã đọc
     */
    public function markNotificationAsRead($notificationId)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Thông báo đã được đánh dấu là đã đọc'
        ]);
    }

    /**
     * Hiển thị danh sách tasks hết hạn
     */
    public function overdueDeadlines()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        // Lấy tasks hết hạn
        $overdueTasks = Task::where('assigned_to', $user->id)
            ->where('status', '!=', 'completed')
            ->whereDate('end_date', '<=', $today)
            ->with('project', 'user')
            ->orderBy('end_date', 'asc')
            ->paginate(15);

        // Lấy tasks sắp hết hạn (3 ngày)
        $upcomingTasks = Task::where('assigned_to', $user->id)
            ->where('status', '!=', 'completed')
            ->whereDate('end_date', '>', $today)
            ->whereDate('end_date', '<=', now()->addDays(3)->toDateString())
            ->with('project', 'user')
            ->orderBy('end_date', 'asc')
            ->get();

        return view('user.deadlines', compact('overdueTasks', 'upcomingTasks'));
    }

    /**
     * Admin: Xem tất cả thông báo deadline từ nhân viên
     */
    public function adminDeadlineNotifications()
    {
        // Chỉ admin mới có quyền xem
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập!');
        }


        $notifications = \DB::table('notifications')
            ->where('type', 'App\Notifications\TaskDeadlineWarning')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Decode data để lấy thông tin chi tiết
        $processedItems = $notifications->map(function ($notif) {
            $data = json_decode($notif->data);
            return (object)[
                'id' => $notif->id,
                'notifiable_id' => $notif->notifiable_id,
                'task_id' => $data->task_id ?? null,
                'task_title' => $data->task_title ?? 'N/A',
                'assigned_user' => $data->assigned_user ?? 'N/A',
                'end_date' => $data->end_date ?? 'N/A',
                'message' => $data->message ?? 'N/A',
                'created_at' => $notif->created_at,
                'read_at' => $notif->read_at,
            ];
        })->all();


        $processedNotifications = new Paginator(
            $processedItems,
            $notifications->perPage(),
            $notifications->currentPage(),
            [
                'path' => route('admin.deadline-notifications'),
            ]
        );

        // Lấy user information
        $userIds = collect($processedItems)->pluck('notifiable_id')->unique();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        return view('admin.deadline-notifications', compact('processedNotifications', 'users'));
    }
}
