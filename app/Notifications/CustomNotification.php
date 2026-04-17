<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class CustomNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $content;
    protected $channel; // 'email' or 'telegram'

    public function __construct($title, $content, $channel = 'email')
    {
        $this->title = $title;
        $this->content = $content;
        $this->channel = $channel;
    }

    public function via(object $notifiable): array
    {
        if ($this->channel === 'telegram') {
            return ['telegram'];
        }
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($this->title)
            ->line($this->content)
            ->line('---')
            ->line('Gửi lúc: ' . now()->format('d/m/Y H:i:s'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    public function toTelegram(object $notifiable): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$botToken || !$chatId) {
            \Log::warning('Telegram bot token or chat ID not configured');
            return;
        }

        $message = "<b>{$this->title}</b>\n\n";
        $message .= htmlspecialchars($this->content) . "\n\n";
        $message .= "🕐 " . now()->format('d/m/Y H:i:s');

        try {
            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }
}
