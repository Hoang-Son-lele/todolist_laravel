<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDeadlineWarning extends Notification
{
    use Queueable;
    protected $task;
    protected $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, $customMessage = null)
    {
        $this->task = $task;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'end_date' => $this->task->end_date->format('d/m/Y'),
            'assigned_user' => $this->task->assignedTo?->name,
            'message' => $this->customMessage ?? 'Task "' . $this->task->title . '" sắp đến hạn (Hạn: ' . $this->task->end_date->format('d/m/Y') . ')',
            'url' => '/my-tasks/' . $this->task->id,
        ];
    }
}
