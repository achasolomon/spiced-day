<?php

namespace App\Notification;

use App\Models\Inspection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class InspectionCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inspection;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inspection $inspection)
    {
        $this->inspection = $inspection;
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
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        $icon = match($this->inspection->overall_result) {
            'pass' => '✅',
            'fail' => '❌',
            'conditional_pass' => '⚠️',
            default => '📋'
        };

        $color = match($this->inspection->overall_result) {
            'pass' => 'green',
            'fail' => 'red',
            'conditional_pass' => 'yellow',
            default => 'gray'
        };

        return [
            'type' => 'inspection_completed',
            'icon' => $icon,
            'color' => $color,
            'title' => 'Inspection Report Available',
            'message' => "Your {$this->inspection->type} has been completed with result: " . 
                        ucwords(str_replace('_', ' ', $this->inspection->overall_result)),
            'inspection_id' => $this->inspection->id,
            'inspection_number' => $this->inspection->inspection_number,
            'overall_result' => $this->inspection->overall_result,
            'overall_score' => $this->inspection->overall_score,
            'items_failed' => $this->inspection->items_failed,
            'requires_action' => $this->inspection->requires_reinspection,
            'action_url' => route('applicant.inspections.show', $this->inspection),
            'action_text' => 'View Report',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}