<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $icon;
    protected $actionUrl;
    protected $actionText;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $title,
        string $message,
        string $icon = 'bell',
        string $actionUrl = null,
        string $actionText = null
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
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
        $data = [
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
        ];

        if ($this->actionUrl) {
            $data['action_url'] = $this->actionUrl;
            $data['action_text'] = $this->actionText ?? 'View Details';
        }

        return $data;
    }
}
