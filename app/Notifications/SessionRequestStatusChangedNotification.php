<?php

namespace App\Notifications;

use App\Models\SessionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionRequestStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly SessionRequest $sessionRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mentor = $this->sessionRequest->mentor;
        $status = ucfirst($this->sessionRequest->status);

        $message = (new MailMessage)
            ->subject('Session Request ' . $status . ' by ' . $mentor->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your session request to ' . $mentor->name . ' has been **' . $status . '**.');

        if ($this->sessionRequest->isAccepted()) {
            $proposed = $this->sessionRequest->proposed_date->format('D, M j Y \a\t g:i A');
            $message->line('📅 Confirmed date: ' . $proposed);
        }

        return $message
            ->action('View My Requests', route('session-requests.index'))
            ->line('Thank you for using MentorHouse.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'               => 'session_request_status_changed',
            'session_request_id' => $this->sessionRequest->id,
            'mentor_name'        => $this->sessionRequest->mentor->name,
            'status'             => $this->sessionRequest->status,
        ];
    }
}
