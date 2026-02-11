<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentRejectedNotification extends Notification
{
    use Queueable;

    protected $assessment;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct($assessment, $reason = null)
    {
        $this->assessment = $assessment;
        $this->reason = $reason;
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
        $message = "Your assessment for {$this->assessment->factory->name} ({$this->assessment->year}) has been rejected.";
        
        if ($this->reason) {
            $message .= " Reason: {$this->reason}";
        }

        return [
            'title' => 'Assessment Rejected',
            'message' => $message,
            'icon' => 'warning',
            'action_url' => route('assessments.show', $this->assessment->id),
            'action_text' => 'Edit Assessment',
        ];
    }
}
