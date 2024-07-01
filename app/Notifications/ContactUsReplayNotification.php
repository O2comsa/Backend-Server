<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContactUsReplayNotification extends Notification
{
    use Queueable;

    protected $name;
    protected $email;
    protected $title;
    protected $message;
    protected $new_message;
    protected $mobile;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $name, $email, $message, $mobile, $new_meeage)
    {
        //
        $this->title = $title;
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
        $this->new_message = $new_meeage;
        $this->mobile = $mobile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting(trans('app.welcome'))
            ->subject('لديك رد على ر سالتك التالية : ' . env('APP_NAME'))
            ->line('لديك رد على ر سالتك التالية : ' . env('APP_NAME'))
            ->line(' البريد الالكتروني : ' . $this->email)
            ->line(' الرساله : ' . $this->message)
            ->line(' رقم الجوال : ' . $this->mobile)
            ->line('---------------------------')
            ->line('الرد : ' . $this->new_message)
            ->line(' تطبيق إشارتي');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
