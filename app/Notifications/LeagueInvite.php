<?php

namespace App\Notifications;

use App\Models\League;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeagueInvite extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public League $league, public ?string $code, public ?string $email)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $greeting = 'Hello,';
        
        // handle notfications where there is a name property
        if (get_class($notifiable) !== 'Illuminate\Notifications\AnonymousNotifiable') {
            $greeting = 'Hello, ' . $notifiable->name;
        }

        // $url = $this->code ? route('otp.register', [$this->league, $this->code, $this->email]) : route('profile.notificationsAndInvites');
        $url = $this->code ? route('otp.register', [$this->league, $this->code, $this->email]) : route('dashboard');

        return (new MailMessage)
                ->subject('You have been invited!')
                ->greeting($greeting)
                ->lineIf($this->code, $this->league->creator->name . ' has invited you to join ' . $this->league->name . ' league at Picker name here! Please, utilize the link below to create a user account and join the league.')
                ->lineIf(!$this->code, 'Your ' . $this->league->name . ' picker name here invite request has been approved! Please, utilize the link below to log in to Picker name here and join the league!')
                ->action('Join the League!', url($url))
                ->line('Thank you for using the application!');
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
