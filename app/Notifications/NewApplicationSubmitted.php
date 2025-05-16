<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

final class NewApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Application $application
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Scholarship Application Submitted')
            ->line('A new scholarship application has been submitted.')
            ->line('Applicant: '.$this->application->user->name)
            ->action('Review Application', route('admin.applications.show', $this->application))
            ->line('Please review the application and verify the submitted documents.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'user_name' => $this->application->user->name,
            'status' => $this->application->status,
        ];
    }
}
