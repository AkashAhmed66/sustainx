<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentSubmittedNotification extends Notification
{
    use Queueable;

    protected $assessment;
    protected $submittedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct($assessment, $submittedBy)
    {
        $this->assessment = $assessment;
        $this->submittedBy = $submittedBy;
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
            'title' => 'New Assessment Submitted for Review',
            'message' => "{$this->submittedBy->name} submitted an assessment for {$this->assessment->factory->name} ({$this->assessment->year} - {$this->assessment->period}) for your review.",
            'icon' => 'info',
            'action_url' => route('assessments.show', $this->assessment->id),
            'action_text' => 'Review Assessment',
        ];
    }
}
