<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Task</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        .btn-back {
            background: #757575;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: #616161;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        input[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
            color: #999;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        button {
            padding: 10px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-right: 10px;
        }

        button:hover {
            background: #5568d3;
        }

        .error-messages {
            margin-bottom: 20px;
        }

        .error-messages span {
            color: #d32f2f;
            font-size: 12px;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Sửa Task #{{ $task->id }}</h1>
            <a href="{{ route('tasks.index') }}" class="btn-back">← Quay Lại</a>
        </header>

        <div class="form-container">
            <form method="POST" action="{{ route('tasks.update', $task->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Tiêu Đề *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" {{ Auth::user()->role !== 'admin' ? 'readonly' : '' }} required>
                    @error('title')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                @if(Auth::user()->role !== 'admin')
                <input type="hidden" name="title" value="{{ $task->title }}">
                @endif

                @if(Auth::user()->role === 'admin')
                <div class="form-group">
                    <label for="project_id">Dự Án</label>
                    <select id="project_id" name="project_id">
                        <option value="">-- Chọn Dự Án (Tuỳ Chọn) --</option>
                        @foreach ($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('project_id')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                @else
                <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                @endif

                <div class="form-group">
                    <label for="description">Mô Tả</label>
                    <textarea id="description" name="description">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                @if(Auth::user()->role === 'admin')
                <div class="form-group">
                    <label for="assigned_to">Giao Cho Nhân Viên *</label>
                    <select id="assigned_to" name="assigned_to" required>
                        <option value="">-- Chọn Nhân Viên --</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
                @else
                <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                @endif

                <div class="form-group">
                    <label for="status">Trạng Thái *</label>
                    <select id="status" name="status" required>
                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Chờ Xử Lý</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>Đang Thực Hiện</option>
                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Hoàn Thành</option>
                    </select>
                    @error('status')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="difficulty">Độ Khó *</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="easy" {{ old('difficulty', $task->difficulty) == 'easy' ? 'selected' : '' }}>Dễ</option>
                        <option value="medium" {{ old('difficulty', $task->difficulty) == 'medium' ? 'selected' : '' }}>Trung Bình</option>
                        <option value="hard" {{ old('difficulty', $task->difficulty) == 'hard' ? 'selected' : '' }}>Khó</option>
                    </select>
                    @error('difficulty')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="completion_percentage">Phần Trăm Hoàn Thành (%)</label>
                    <input type="range" id="completion_percentage" name="completion_percentage" min="0" max="100" value="{{ old('completion_percentage', $task->completion_percentage) }}" style="padding: 0;">
                    <div style="text-align: center; margin-top: 10px; font-weight: bold;">
                        <span id="percentageDisplay">{{ old('completion_percentage', $task->completion_percentage) }}</span>%
                        <div style="background: #e0e0e0; height: 10px; border-radius: 5px; overflow: hidden; margin-top: 10px;">
                            <div id="progressBar" style="background: #4caf50; height: 100%; width: {{ old('completion_percentage', $task->completion_percentage) }}%; border-radius: 5px; transition: width 0.3s;"></div>
                        </div>
                    </div>
                    @error('completion_percentage')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_date">Ngày Bắt Đầu</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}">
                    @error('start_date')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date">Ngày Kết Thúc</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $task->end_date?->format('Y-m-d')) }}">
                    @error('end_date')
                    <span style="color: #d32f2f; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit">Cập Nhật Task</button>
                <a href="{{ route('tasks.index') }}" style="display: inline-block; padding: 10px 30px; background: #757575; color: white; text-decoration: none; border-radius: 5px;">Hủy</a>
            </form>
        </div>
    </div>

    <script>
        const slider = document.getElementById('completion_percentage');
        const percentageDisplay = document.getElementById('percentageDisplay');
        const progressBar = document.getElementById('progressBar');

        if (slider) {
            slider.addEventListener('input', function() {
                percentageDisplay.textContent = this.value;
                progressBar.style.width = this.value + '%';
            });
        }
    </script>
</body>

</html>