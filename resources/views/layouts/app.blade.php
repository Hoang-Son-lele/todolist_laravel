<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BigBang - Quản Lý Dự Án')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            position: relative;
            padding-bottom: 2px;
            transition: color 0.3s ease;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: white;
            transition: width 0.3s ease;
        }

        .nav-link:hover:after {
            width: 100%;
        }

        .active-link {
            border-bottom: 2px solid white;
        }

        .sidebar-menu {
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
            min-height: calc(100vh - 70px);
        }

        .menu-item {
            padding: 12px 20px;
            border-left: 3px solid transparent;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background: #e9ecef;
            border-left-color: #667eea;
            padding-left: 25px;
        }

        .menu-item.active {
            background: #667eea;
            color: white;
            border-left-color: #667eea;
        }

        .content-wrapper {
            background: #f5f7fa;
            min-height: calc(100vh - 70px);
            padding: 30px 20px;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="navbar sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-8">
                    <a href="/" class="text-white font-bold text-2xl flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        BigBang
                    </a>
                    @auth
                    <div class="hidden md:flex gap-6">
                        <a href="{{ route('projects.index') }}" class="nav-link text-white hover:text-gray-100 {{ request()->routeIs('projects.*') ? 'active-link' : '' }}">
                            <i class="fas fa-folder"></i> Dự Án
                        </a>
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('tasks.index') }}" class="nav-link text-white hover:text-gray-100 {{ request()->routeIs('tasks.*') ? 'active-link' : '' }}">
                            <i class="fas fa-tasks"></i> Tasks
                        </a>
                        @endif
                    </div>
                    @endauth
                </div>

                <div class="flex items-center gap-6">
                    @auth
                    <div class="flex items-center gap-3">
                        <!-- Notification Bell -->
                        @include('components.notification-bell')

                        <div class="text-right">
                            <p class="text-white font-semibold text-sm">{{ Auth::user()->name }}</p>
                            @if(Auth::user()->role === 'admin')
                            <span class="text-yellow-200 text-xs">Admin</span>
                            @else
                            <span class="text-gray-200 text-xs">Nhân viên</span>
                            @endif
                        </div>
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:text-gray-200 transition">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                    @else
                    <a href="{{ route('auth.login') }}" class="text-white hover:text-gray-200 transition">
                        Đăng Nhập
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex">
        @auth
        <!-- Sidebar -->
        <div class="hidden lg:block w-64 sidebar-menu">
            <div class="p-6 border-b">
                <p class="text-gray-600 text-sm font-semibold">MENU</p>
            </div>
            <a href="{{ route('projects.index') }}" class="menu-item {{ request()->routeIs('projects.index') || request()->routeIs('projects.show') ? 'active' : '' }}">
                <i class="fas fa-folder"></i>
                <span>Danh Sách Dự Án</span>
            </a>
            <a href="{{ route('user.overdue-deadlines') }}" class="menu-item {{ request()->routeIs('user.overdue-deadlines') ? 'active' : '' }}">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <span>Cảnh Báo Deadline</span>
            </a>
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('projects.create') }}" class="menu-item {{ request()->routeIs('projects.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i>
                <span>Tạo Dự Án Mới</span>
            </a>
            @endif
            @if(Auth::user()->role === 'admin')
            <div class="border-t mt-2">
                <p class="text-gray-600 text-sm font-semibold px-6 py-4">QUẢN LÝ</p>
                <a href="{{ route('tasks.index') }}" class="menu-item {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Danh Sách Tasks</span>
                </a>
                <a href="{{ route('tasks.create') }}" class="menu-item {{ request()->routeIs('tasks.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tạo Task Mới</span>
                </a>
                <a href="{{ route('admin.deadline-notifications') }}" class="menu-item {{ request()->routeIs('admin.deadline-notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell text-red-500"></i>
                    <span>Thông Báo Deadline</span>
                </a>

                <div class="menu-label text-gray-400 text-xs uppercase font-semibold mb-2 px-4 mt-4">
                    Reports
                </div>

                <a href="{{ route('projects.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-500 hover:text-white transition-colors">
                    <i class="fas fa-chart-line mr-3"></i> <span>Project Timelines</span>
                </a>

            </div>
            @endif
        </div>
        @endauth

        <!-- Content Area -->
        <div class="flex-1">
            <div class="content-wrapper">
                @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                    <p class="font-bold">Có lỗi xảy ra:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Mobile Menu Toggle (Optional) -->
    <script>
        // Simple mobile menu toggle if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Add any JavaScript functionality here
        });
    </script>
</body>

</html>