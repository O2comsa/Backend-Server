<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\LiveEvent;

class TestEmail extends Notification
{
    use Queueable;
    protected $certificate;

    /**
     * Create a new notification instance.
     */
    public function __construct($certificate)
    {
        //
        $this->certificate = $certificate;
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
        if ($this->certificate->related instanceof LiveEvent) {
            return (new MailMessage)
                ->subject('تم إصدار الشهادة الخاصة بكم')
                ->greeting('مرحبا, ' . $notifiable->name )
                ->line('تم إصدار شهادة ' . $this->certificate->related->name)
                ->action('حمل الشهادة الأن', url($this->certificate->file_pdf))
                ->line('شكرا لاستعمال تطبيق اشارتي');
        } else {
            return (new MailMessage)

                ->subject('تم إصدار الشهادة الخاصة بكم')
                ->greeting('مرحبا, ' . $notifiable->name )
                ->line('تم إصدار شهادة ' . $this->certificate->related->title)
                ->action('حمل الشهادة الأن', url($this->certificate->file_pdf))
                ->line('شكرا لاستعمال تطبيق اشارتي');
        }
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
