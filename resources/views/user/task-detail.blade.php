@extends('layouts.app')

@section('title', 'Chi Tiết Task')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Chi Tiết Task</h1>
        <div class="flex gap-2">
            <button onclick="toggleEditForm()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                ✏️ Sửa
            </button>
            @if($task->project)
            <a href="{{ route('projects.show', $task->project) }}" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded">
                📁 Dự Án
            </a>
            @endif
            <a href="{{ route('user.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
                ← Quay Lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-6 text-white">
            <h2 class="text-2xl font-bold mb-2">{{ $task->title }}</h2>
            <div class="flex gap-4">
                @if($task->difficulty === 'easy')
                <span class="inline-block px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                    ⭐ Dễ
                </span>
                @elseif($task->difficulty === 'medium')
                <span class="inline-block px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                    ⭐⭐ Trung Bình
                </span>
                @else
                <span class="inline-block px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                    ⭐⭐⭐ Khó
                </span>
                @endif

                @if($task->status === 'pending')
                <span class="inline-block px-3 py-1 bg-yellow-500 bg-opacity-30 rounded-full text-sm font-semibold">
                    ⏳ Chờ Xử Lý
                </span>
                @elseif($task->status === 'in_progress')
                <span class="inline-block px-3 py-1 bg-blue-400 bg-opacity-30 rounded-full text-sm font-semibold">
                    🔄 Đang Thực Hiện
                </span>
                @else
                <span class="inline-block px-3 py-1 bg-green-400 bg-opacity-30 rounded-full text-sm font-semibold">
                    ✅ Hoàn Thành
                </span>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Quick Edit Form -->
            <div id="editForm" class="mb-6 p-4 bg-yellow-50 rounded-lg border-2 border-yellow-300 hidden">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Chỉnh Sửa Thông Tin Task</h3>
                <form action="{{ route('user.task.update', $task->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mô Tả</label>
                        <textarea name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $task->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Độ Khó</label>
                        <select name="difficulty" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="easy" {{ $task->difficulty === 'easy' ? 'selected' : '' }}>Dễ</option>
                            <option value="medium" {{ $task->difficulty === 'medium' ? 'selected' : '' }}>Trung Bình</option>
                            <option value="hard" {{ $task->difficulty === 'hard' ? 'selected' : '' }}>Khó</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày Bắt Đầu</label>
                            <input type="date" name="start_date" value="{{ $task->start_date?->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày Kết Thúc</label>
                            <input type="date" name="end_date" value="{{ $task->end_date?->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold">
                            ✅ Lưu
                        </button>
                        <button type="button" onclick="toggleEditForm()" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold">
                            ❌ Hủy
                        </button>
                    </div>
                </form>
            </div>

            <!-- Project Info -->
            @if($task->project)
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                <p class="text-sm text-gray-600 mb-1">Dự Án</p>
                <a href="{{ route('projects.show', $task->project) }}" class="text-lg font-semibold text-blue-600 hover:underline">
                    {{ $task->project->name }}
                </a>
                <p class="text-sm text-gray-600 mt-2">
                    📅 Kết thúc: {{ $task->project->end_date->format('d/m/Y') }}
                </p>
            </div>
            @endif

            <!-- Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Mô Tả</h3>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    @if($task->description)
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $task->description }}</p>
                    @else
                    <p class="text-gray-400 italic">Không có mô tả</p>
                    @endif
                </div>
            </div>

            <!-- Status & Completion Update Form -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Cập Nhật Trạng Thái & Phần Trăm Hoàn Thành</h3>
                <form action="{{ route('user.task.update-status', $task->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Status Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng Thái</label>
                        <div class="flex gap-3 flex-wrap">
                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="status" value="pending" {{ $task->status === 'pending' ? 'checked' : '' }} class="w-4 h-4">
                                <span class="text-gray-700">⏳ Chờ Xử Lý</span>
                            </label>

                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="status" value="in_progress" {{ $task->status === 'in_progress' ? 'checked' : '' }} class="w-4 h-4">
                                <span class="text-gray-700">🔄 Đang Thực Hiện</span>
                            </label>

                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="status" value="completed" {{ $task->status === 'completed' ? 'checked' : '' }} class="w-4 h-4">
                                <span class="text-gray-700">✅ Hoàn Thành</span>
                            </label>
                        </div>
                    </div>

                    <!-- Completion Percentage -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phần Trăm Hoàn Thành</label>
                        <div class="flex items-center gap-4">
                            <input type="range" name="completion_percentage" min="0" max="100" value="{{ $task->completion_percentage }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" id="percentageSlider">
                            <span class="font-semibold text-gray-700 whitespace-nowrap">
                                <span id="percentageDisplay">{{ $task->completion_percentage }}</span>%
                            </span>
                        </div>
                        <div class="mt-3 w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-300" id="progressBar" style="width: {{ $task->completion_percentage }}%"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold">
                        💾 Lưu Trạng Thái & Phần Trăm
                    </button>
                </form>
            </div>

            <!-- Task Info -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Người Tạo</p>
                    <p class="font-semibold text-gray-800">{{ $task->user->name }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Ngày Bắt Đầu</p>
                    <p class="font-semibold text-gray-800">
                        @if($task->start_date)
                        {{ $task->start_date->format('d/m/Y') }}
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Ngày Kết Thúc</p>
                    <p class="font-semibold text-gray-800">
                        @if($task->end_date)
                        {{ $task->end_date->format('d/m/Y') }}
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Ngày Tạo</p>
                    <p class="font-semibold text-gray-800">{{ $task->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">ID Task</p>
                    <p class="font-semibold text-gray-800">#{{ $task->id }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const slider = document.getElementById('percentageSlider');
    const display = document.getElementById('percentageDisplay');
    const progressBar = document.getElementById('progressBar');

    if (slider) {
        slider.addEventListener('input', function() {
            display.textContent = this.value;
            progressBar.style.width = this.value + '%';
        });
    }

    function toggleEditForm() {
        const editForm = document.getElementById('editForm');
        editForm.classList.toggle('hidden');
    }
</script>
@endsection