@extends('layouts.app')

@section('title', 'Quản Lý Deadline - Cảnh Báo Hết Hạn')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">⚠️ Quản Lý Deadline</h1>
        <p class="text-gray-600">Quản lý và gửi email thông báo cho quản lý về tasks hết hạn</p>
    </div>

    <!-- Overdue Tasks Section -->
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-red-500">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h2 class="text-2xl font-bold text-red-700">
                    <i class="fas fa-fire"></i> Tasks Đã Hết Hạn
                </h2>
                <p class="text-red-600 text-sm mt-1">{{ $overdueTasks->total() }} task đã vượt quá thời hạn</p>
            </div>

            @if ($overdueTasks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tiêu Đề</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Dự Án</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ngày Kết Thúc</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Quá Hạn</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Trạng Thái</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overdueTasks as $task)
                        <tr class="border-b border-gray-200 hover:bg-red-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('user.task.view', $task) }}" class="text-blue-600 hover:underline font-semibold">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->project)
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                    {{ $task->project->name }}
                                </span>
                                @else
                                <span class="text-gray-400 text-sm">Không có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-red-600">
                                {{ $task->end_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-3 py-1 bg-red-500 text-white rounded-full text-xs font-semibold">
                                    {{ now()->diffInDays($task->end_date, false) }} ngày
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->status === 'pending')
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                    ⏳ Chờ Xử Lý
                                </span>
                                @elseif($task->status === 'in_progress')
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                    🔄 Đang Thực Hiện
                                </span>
                                @else
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    ✅ Hoàn Thành
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('user.task.view', $task) }}" class="inline-block px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold">
                                        <i class="fas fa-eye"></i> Chi Tiết
                                    </a>
                                    <button onclick="sendEmailConfirm({{ $task->id }})" class="inline-block px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-semibold">
                                        <i class="fas fa-envelope"></i> Email
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $overdueTasks->links() }}
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-check-circle text-green-400 text-6xl mb-4"></i>
                <p class="text-gray-600 text-lg font-semibold">🎉 Tuyệt vời! Không có task nào hết hạn</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Upcoming Tasks Section -->
    @if($upcomingTasks->count() > 0)
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-yellow-500">
            <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
                <h2 class="text-2xl font-bold text-yellow-700">
                    <i class="fas fa-clock"></i> Tasks Sắp Hết Hạn (3 ngày tới)
                </h2>
                <p class="text-yellow-600 text-sm mt-1">{{ $upcomingTasks->count() }} task sắp đạt hạn chót</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-yellow-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tiêu Đề</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Dự Án</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ngày Kết Thúc</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Còn Lại</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Trạng Thái</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($upcomingTasks as $task)
                        <tr class="border-b border-gray-200 hover:bg-yellow-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('user.task.view', $task) }}" class="text-blue-600 hover:underline font-semibold">
                                    {{ $task->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->project)
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                    {{ $task->project->name }}
                                </span>
                                @else
                                <span class="text-gray-400 text-sm">Không có</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-yellow-600">
                                {{ $task->end_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-3 py-1 bg-yellow-500 text-white rounded-full text-xs font-semibold">
                                    {{ now()->diffInDays($task->end_date) }} ngày
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($task->status === 'pending')
                                <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                    ⏳ Chờ Xử Lý
                                </span>
                                @elseif($task->status === 'in_progress')
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                    🔄 Đang Thực Hiện
                                </span>
                                @else
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    ✅ Hoàn Thành
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('user.task.view', $task) }}" class="inline-block px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold">
                                        <i class="fas fa-eye"></i> Chi Tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    let currentTaskId = null;

    function sendEmailConfirm(taskId) {
        currentTaskId = taskId;
        document.getElementById('messageModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('messageModal').classList.add('hidden');
        document.getElementById('customMessage').value = '';
        currentTaskId = null;
    }

    function submitMessage() {
        const message = document.getElementById('customMessage').value.trim();
        if (message.length < 5) {
            alert('Nội dung phải tối thiểu 5 ký tự');
            return;
        }

        closeModal();
        sendEmail(currentTaskId, message);
    }

    function sendEmail(taskId, message) {
        fetch(`/api/tasks/${taskId}/send-deadline-email`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    custom_message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi khi gửi thông báo');
            });
    }

    // Close modal khi click outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('messageModal');
        if (event.target === modal) {
            closeModal();
        }
    });

    // Enter key to submit
    document.getElementById('customMessage')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            submitMessage();
        }
    });
</script>

<!-- Modal for message input -->
<div id="messageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-comment"></i> Gửi Thông Báo Cho Quản Lý
            </h3>
        </div>

        <div class="px-6 py-4">
            <p class="text-gray-600 text-sm mb-4">Nhập nội dung thông báo về task hết hạn:</p>
            <textarea
                id="customMessage"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                rows="5"
                placeholder="Ví dụ: Task này đã vượt quá hạn chót 2 ngày. Vui lòng cân nhắc gia hạn hoặc hỗ trợ thêm..."></textarea>
            <p class="text-gray-500 text-xs mt-2">Tối thiểu 5 ký tự, Ctrl+Enter để gửi</p>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded font-semibold transition">
                Hủy
            </button>
            <button onclick="submitMessage()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded font-semibold transition">
                <i class="fas fa-send"></i> Gửi Thông Báo
            </button>
        </div>
    </div>
</div>

@endsection