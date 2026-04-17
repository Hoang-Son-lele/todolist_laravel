@extends('layouts.app')

@section('title', 'Dashboard - Quản Lý Tasks')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📋 Dashboard - Tasks Của Tôi</h1>
        <p class="text-gray-600">Quản lý và cập nhật trạng thái tasks được giao</p>
    </div>

    <!-- Quick Send Notification Form -->
    <div class="mb-8 bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">📢 Gửi Thông Báo Nhanh</h2>
            <p class="text-gray-600 text-sm">Gửi thông báo tới email hoặc Telegram của bạn</p>
        </div>

        <form id="quickNotificationForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="notification_title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tiêu Đề
                    </label>
                    <input
                        type="text"
                        id="notification_title"
                        name="title"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ví dụ: Nhắc nhở project"
                        required
                        minlength="5"
                        maxlength="200">
                </div>

                <div>
                    <label for="notification_channel" class="block text-sm font-semibold text-gray-700 mb-2">
                        Gửi Tới
                    </label>
                    <select id="notification_channel" name="channel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="email">📧 Email</option>
                        <option value="telegram">💬 Telegram</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="notification_content" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nội Dung
                </label>
                <textarea
                    id="notification_content"
                    name="content"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    placeholder="Nhập nội dung thông báo..."
                    required
                    minlength="10"
                    maxlength="5000"></textarea>
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition"
                    id="sendNotificationBtn">
                    <i class="fas fa-paper-plane"></i> Gửi Ngay
                </button>
                <button
                    type="reset"
                    class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg font-semibold transition">
                    <i class="fas fa-times"></i> Xóa
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Tổng Tasks</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                </div>
                <i class="fas fa-tasks text-blue-500 text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Chờ Xử Lý</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <i class="fas fa-hourglass-start text-yellow-500 text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Đang Thực Hiện</p>
                    <p class="text-3xl font-bold text-blue-400">{{ $stats['in_progress'] }}</p>
                </div>
                <i class="fas fa-spinner text-blue-400 text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Hoàn Thành</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-4xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">Danh Sách Tasks</h2>
        </div>

        @if ($tasks->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tiêu Đề</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Dự Án</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Trạng Thái</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Độ Khó</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Phần Trăm Hoàn Thành</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ngày Bắt Đầu</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Ngày Kết Thúc</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
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
                        <td class="px-6 py-4">
                            <form action="{{ route('user.task.update-status', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="px-3 py-1 rounded-full text-sm font-semibold border-2 cursor-pointer
                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800 border-green-300' : '' }}
                                    {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800 border-blue-300' : '' }}
                                    {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : '' }}
                                ">
                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Chờ Xử Lý</option>
                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>Đang Thực Hiện</option>
                                    <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Hoàn Thành</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->difficulty === 'easy')
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-star"></i> Dễ
                            </span>
                            @elseif($task->difficulty === 'medium')
                            <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i> Trung Bình
                            </span>
                            @else
                            <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> Khó
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $task->completion_percentage }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">{{ $task->completion_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($task->start_date)
                            {{ $task->start_date->format('d/m/Y') }}
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($task->end_date)
                            <div class="flex items-center gap-2">
                                <span>{{ $task->end_date->format('d/m/Y') }}</span>
                                @if($task->end_date <= now()->toDateString() && $task->status !== 'completed')
                                    <span class="inline-block px-2 py-1 bg-red-500 text-white rounded text-xs font-semibold" title="Đã hết hạn">
                                        <i class="fas fa-exclamation-circle"></i> Hết hạn
                                    </span>
                                    @elseif($task->end_date <= now()->addDays(3)->toDateString() && $task->status !== 'completed')
                                        <span class="inline-block px-2 py-1 bg-yellow-500 text-white rounded text-xs font-semibold" title="Sắp hết hạn">
                                            <i class="fas fa-clock"></i> Sắp hạn
                                        </span>
                                        @endif
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('user.task.view', $task) }}" class="inline-block px-4 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold">
                                    <i class="fas fa-eye"></i> Chi Tiết
                                </a>
                                @if($task->end_date && $task->end_date <= now()->toDateString() && $task->status !== 'completed')
                                    <button onclick="sendEmailQuick(event, {{ $task->id }})" class="inline-block px-4 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-semibold" title="Gửi email cho quản lý">
                                        <i class="fas fa-envelope"></i> Email
                                    </button>
                                    @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tasks->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Hiện tại bạn chưa có task nào được giao</p>
        </div>
        @endif
    </div>

    <!-- Quick Fix Script for Select -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('select[name="status"]');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });

            // Handle quick notification form
            const notificationForm = document.getElementById('quickNotificationForm');
            if (notificationForm) {
                notificationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    sendQuickNotification();
                });
            }
        });

        // Gửi thông báo nhanh
        function sendQuickNotification() {
            const title = document.getElementById('notification_title').value;
            const content = document.getElementById('notification_content').value;
            const channel = document.getElementById('notification_channel').value;
            const btn = document.getElementById('sendNotificationBtn');

            if (!title || !content || !channel) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }

            btn.disabled = true;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

            fetch('/api/notifications/send-quick', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: title,
                        content: content,
                        channel: channel
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        document.getElementById('quickNotificationForm').reset();
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    } else {
                        alert('Lỗi: ' + data.message);
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi gửi thông báo: ' + error.message);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                });
        }

        // Gửi email cho quản lý từ dashboard
        function sendEmailQuick(event, taskId) {
            event.preventDefault();

            if (!confirm('Gửi email thông báo task hết hạn cho quản lý?')) {
                return;
            }

            const button = event.target.closest('button');
            button.disabled = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

            fetch(`/api/tasks/${taskId}/send-deadline-email`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        button.innerHTML = '<i class="fas fa-check"></i> Đã gửi';
                        button.classList.remove('bg-red-500', 'hover:bg-red-600');
                        button.classList.add('bg-green-500', 'hover:bg-green-600');
                    } else {
                        alert('Lỗi: ' + data.message);
                        button.disabled = false;
                        button.innerHTML = originalHTML;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi gửi email');
                    button.disabled = false;
                    button.innerHTML = originalHTML;
                });
        }
    </script>
</div>
@endsection