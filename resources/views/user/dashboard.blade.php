@extends('layouts.app')

@section('title', 'Dashboard - Quản Lý Tasks')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">📋 Dashboard - Tasks Của Tôi</h1>
        <p class="text-gray-600">Quản lý và cập nhật trạng thái tasks được giao</p>
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
                            {{ $task->end_date->format('d/m/Y') }}
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('user.task.view', $task) }}" class="inline-block px-4 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm font-semibold">
                                <i class="fas fa-eye"></i> Chi Tiết
                            </a>
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
        });
    </script>
</div>
@endsection