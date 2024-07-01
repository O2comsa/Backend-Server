<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContactUsAdminNotification extends Notification
{
    use Queueable;
    protected $name;
    protected $email;
    protected $message;
    protected $mobile;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $email, $message,$mobile)
    {
        //
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
        $this->mobile = $mobile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting(trans('app.welcome'))
            ->subject(' لديك رساله جديده من : ' . $this->name)
            ->line(' لديك رساله جديده من : ' . $this->name)
            ->line(' البريد الالكتروني : ' . $this->email)
            ->line(' الرساله : ' . $this->message)
            ->line(' رقم الجوال : ' . $this->mobile)
            ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
