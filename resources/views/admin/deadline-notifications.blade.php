@extends('layouts.app')

@section('title', 'Admin - Thông Báo Deadline')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📊 Thông Báo Deadline Từ Nhân Viên</h1>
        <p class="text-gray-600">Xem tất cả thông báo về tasks hết hạn từ các nhân viên</p>
    </div>

    <!-- Notifications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-bell"></i> Danh Sách Thông Báo ({{ $processedNotifications->count() }})
            </h2>
        </div>

        @if ($processedNotifications->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Thời Gian</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nhân Viên</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Task</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Hạn Chót</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nội Dung Thông Báo</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($processedNotifications as $notif)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($notif->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if(isset($users[$notif->notifiable_id]))
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                    {{ strtoupper(substr($users[$notif->notifiable_id]->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $users[$notif->notifiable_id]->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $users[$notif->notifiable_id]->email }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                                {{ $notif->task_title }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-red-600">
                            {{ $notif->end_date }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <p class="text-sm text-gray-700 line-clamp-2" title="{{ $notif->message }}">
                                    {{ $notif->message }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($notif->read_at)
                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                ✓ Đã đọc
                            </span>
                            @else
                            <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                ⚠ Chưa đọc
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $processedNotifications->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-600 text-lg font-semibold">Không có thông báo nào</p>
        </div>
        @endif
    </div>

    <!-- Stats Cards -->
    @php
    $totalNotifications = $processedNotifications->count();
    $unreadNotifications = $processedNotifications->where('read_at', null)->count();
    $readNotifications = $processedNotifications->where('read_at', '!=', null)->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Tổng Thông Báo</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalNotifications }}</p>
                </div>
                <i class="fas fa-bell text-blue-500 text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Chưa Đọc</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $unreadNotifications }}</p>
                </div>
                <i class="fas fa-exclamation-circle text-yellow-500 text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-600 text-sm">Đã Đọc</p>
                    <p class="text-3xl font-bold text-green-600">{{ $readNotifications }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-4xl opacity-20"></i>
            </div>
        </div>
    </div>
</div>
@endsection