<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AudioUpdate extends Notification
{
    use Queueable;

    public $content, $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $content)
    {
        $this->user = $user;
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->content['header'])
            ->view('mail.audio_status_change', [
                'user' =>  $this->user,
                'content' =>  $this->content
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->content;
    }
}
