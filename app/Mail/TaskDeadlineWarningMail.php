<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDeadlineWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, $customMessage = null)
    {
        $this->task = $task;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Thông báo: Task "' . $this->task->title . '" đã hết hạn',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.task-deadline-warning',
            with: [
                'task' => $this->task,
                'customMessage' => $this->customMessage,
                'assignedUser' => $this->task->assignedTo,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
