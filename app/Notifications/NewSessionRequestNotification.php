<?php

namespace App\Notifications;

use App\Models\SessionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSessionRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly SessionRequest $sessionRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mentee   = $this->sessionRequest->mentee;
        $proposed = $this->sessionRequest->proposed_date->format('D, M j Y \a\t g:i A');

        return (new MailMessage)
            ->subject('New Session Request from ' . $mentee->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($mentee->name . ' has sent you a session request.')
            ->line('**Proposed date:** ' . $proposed)
            ->when($this->sessionRequest->message, fn ($m) =>
                $m->line('**Message:** ' . $this->sessionRequest->message)
            )
            ->action('View Request', route('session-requests.index'))
            ->line('Please review and respond at your earliest convenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'               => 'new_session_request',
            'session_request_id' => $this->sessionRequest->id,
            'mentee_name'        => $this->sessionRequest->mentee->name,
            'proposed_date'      => $this->sessionRequest->proposed_date->toDateTimeString(),
        ];
    }
}
