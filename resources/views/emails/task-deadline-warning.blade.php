@component('mail::message')
# ⚠️ Thông Báo Task Hết Hạn

Xin chào **{{ $task->user->name }}**,

Nhân viên **{{ $assignedUser->name }}** đã gửi thông báo về task hết hạn:

@component('mail::panel')
**📋 Tiêu đề Task:** {{ $task->title }}

**📅 Ngày hết hạn:** {{ $task->end_date->format('d/m/Y') }}

**📊 Dự án:** {{ $task->project?->name ?? 'Không có' }}

**🔄 Trạng thái:**
@if($task->status === 'completed')
✅ Hoàn Thành
@elseif($task->status === 'in_progress')
🔄 Đang Thực Hiện
@else
⏳ Chờ Xử Lý
@endif

**💬 Thông báo từ nhân viên:**
> {{ $customMessage ?? 'Không có thông báo bổ sung' }}
@endcomponent

@component('mail::button', ['url' => route('user.task.view', $task)])
Xem Chi Tiết Task
@endcomponent

---

**Thông tin nhân viên:**
- **Tên:** {{ $assignedUser->name }}
- **Email:** {{ $assignedUser->email }}

Cảm ơn,
{{ config('app.name') }}
@endcomponent