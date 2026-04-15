<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Task</title>
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
            max-width: 1200px;
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

        .user-info {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-info span {
            color: #666;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-danger {
            background: #d32f2f;
            color: white;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        .btn-secondary {
            background: #757575;
            color: white;
        }

        .btn-secondary:hover {
            background: #616161;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .task-table {
            background: white;
            border-collapse: collapse;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .task-table th {
            background: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        .task-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .task-table tr:hover {
            background: #f9f9f9;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #664d03;
        }

        .status-in_progress {
            background: #cfe2ff;
            color: #084298;
        }

        .status-completed {
            background: #d1e7dd;
            color: #0f5132;
        }

        .difficulty {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .difficulty-easy {
            background: #d1e7dd;
            color: #0f5132;
        }

        .difficulty-medium {
            background: #fff3cd;
            color: #664d03;
        }

        .difficulty-hard {
            background: #f8d7da;
            color: #842029;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-edit {
            background: #2196F3;
            color: white;
            text-decoration: none;
        }

        .btn-edit:hover {
            background: #0b7dda;
        }

        .btn-delete {
            background: #d32f2f;
            color: white;
            border: none;
            cursor: pointer;
        }

        .no-tasks {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 5px;
        }

        .pagination {
            display: flex;
            gap: 5px;
            margin-top: 20px;
            justify-content: center;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-decoration: none;
            background: white;
        }

        .pagination a:hover {
            background: #667eea;
            color: white;
        }

        .pagination .active {
            background: #667eea;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>Quản Lý Task</h1>
            <div class="user-info">
                <span>👤 {{ Auth::user()->name }}
                    @if(Auth::user()->role === 'admin')
                    <strong style="color: #d32f2f;">(Admin)</strong>
                    @endif
                </span>
                <form method="POST" action="{{ route('auth.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Đăng Xuất</button>
                </form>
            </div>
        </header>

        @if (session('success'))
        <div class="success">✓ {{ session('success') }}</div>
        @endif

        @if(Auth::user()->role === 'admin')
        <div style="margin-bottom: 20px;">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">+ Tạo Task Mới</a>
        </div>
        @endif

        @if ($tasks->count() > 0)
        <table class="task-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu Đề</th>
                    <th>Mô Tả</th>
                    <th>Dự Án</th>
                    @if(Auth::user()->role === 'admin')
                    <th>Người Tạo</th>
                    @endif
                    <th>Giao Cho</th>
                    <th>Trạng Thái</th>
                    <th>Độ Khó</th>
                    <th>Phần Trăm Hoàn Thành</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                <tr>
                    <td>#{{ $task->id }}</td>
                    <td><strong>{{ $task->title }}</strong></td>
                    <td>{{ Str::limit($task->description, 50) }}</td>
                    <td>
                        @if($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" style="color: #667eea; text-decoration: none;">{{ $task->project->name }}</a>
                        @else
                        <span style="color: #999;">Không có</span>
                        @endif
                    </td>
                    @if(Auth::user()->role === 'admin')
                    <td>{{ $task->user->name }}</td>
                    @endif
                    <td>
                        @if($task->assignedTo)
                        <strong style="color: #2196F3;">{{ $task->assignedTo->name }}</strong>
                        @else
                        <span style="color: #999;">Chưa giao</span>
                        @endif
                    </td>
                    <td>
                        <span class="status status-{{ $task->status }}">
                            @switch($task->status)
                            @case('pending')
                            Chờ Xử Lý
                            @break
                            @case('in_progress')
                            Đang Thực Hiện
                            @break
                            @case('completed')
                            Hoàn Thành
                            @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <span class="difficulty difficulty-{{ $task->difficulty }}">
                            @switch($task->difficulty)
                            @case('easy')
                            Dễ
                            @break
                            @case('medium')
                            Trung Bình
                            @break
                            @case('hard')
                            Khó
                            @break
                            @endswitch
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; background: #e0e0e0; height: 6px; border-radius: 3px; overflow: hidden;">
                                <div style="background: #4caf50; height: 100%; width: {{ $task->completion_percentage }}%; border-radius: 3px; transition: width 0.3s;"></div>
                            </div>
                            <span style="font-weight: bold; min-width: 40px;">{{ $task->completion_percentage }}%</span>
                        </div>
                    </td>
                    <td>
                        @if($task->start_date)
                        {{ $task->start_date->format('d/m/Y') }}
                        @else
                        <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($task->end_date)
                        {{ $task->end_date->format('d/m/Y') }}
                        @else
                        <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            @if(Auth::user()->role === 'admin' || Auth::user()->id === $task->assigned_to)
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-edit btn-sm">Sửa</a>
                            @endif
                            @if(Auth::user()->role === 'admin')
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa task này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete btn-sm">Xóa</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $tasks->links() }}
        </div>
        @else
        <div class="no-tasks">
            <p style="color: #999; font-size: 18px;">Không có task nào. <a href="{{ route('tasks.create') }}">Tạo task mới</a></p>
        </div>
        @endif

        <a href="{{route('projects.index')}}">
            <button class="btn btn-secondary mt-6">← Quay Lại Dự Án</button>
        </a>
    </div>
</body>

</html>