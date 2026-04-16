<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Project and Task routes
Route::middleware('auth')->group(function () {
    // User dashboard routes
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/overdue-deadlines', [UserController::class, 'overdueDeadlines'])->name('user.overdue-deadlines');
    Route::get('/my-tasks/{taskId}', [UserController::class, 'viewTask'])->name('user.task.view');
    Route::post('/my-tasks/{taskId}/status', [UserController::class, 'updateTaskStatus'])->name('user.task.update-status');
    Route::post('/my-tasks/{taskId}/completion', [UserController::class, 'updateTaskCompletion'])->name('user.task.update-completion');
    Route::post('/my-tasks/{taskId}', [UserController::class, 'updateTask'])->name('user.task.update');
    Route::post('/api/tasks/{task}/status', [UserController::class, 'updateTaskStatusAjax'])->name('api.task.update-status');

    // Notification routes
    Route::get('/api/notifications', [UserController::class, 'getNotifications'])->name('api.notifications');
    Route::post('/api/notifications/{notificationId}/read', [UserController::class, 'markNotificationAsRead'])->name('api.notification.read');
    Route::post('/api/tasks/{taskId}/send-deadline-email', [UserController::class, 'sendDeadlineEmailToManager'])->name('api.task.send-deadline-email');

    // Project routes
    Route::resource('projects', ProjectController::class);

    // Task routes (Admin only)
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/projects/{project}/report', [ProjectController::class, 'report'])
        ->name('projects.report')
        ->middleware(['auth']);

    // Admin routes
    Route::get('/admin/deadline-notifications', [UserController::class, 'adminDeadlineNotifications'])->name('admin.deadline-notifications');
});
