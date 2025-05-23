<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\LiveEvent;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SuccessfullyGeneratedCertEmail extends Notification
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
            Log::info("message email send successfully");
            return (new MailMessage)
                ->subject('تم إصدار الشهادة الخاصة بكم')
                ->greeting('مرحباً, ' . $notifiable->name )
                ->line('تم إصدار شهادة ' . $this->certificate->related->name)
              //  ->line(' أطيب التحيات')
                ->line(' تطبيق إشارتي')
                ->attach($this->certificate->file_pdf);
        } else {
            Log::info("message email send successfully");
            return (new MailMessage)

                ->subject('تم إصدار الشهادة الخاصة بكم')
                ->greeting('مرحباً, ' . $notifiable->name )
                ->line('تم إصدار شهادة ' . $this->certificate->related->title)
              //  ->line(' أطيب التحيات')
                ->line(' تطبيق إشارتي')
                ->attach($this->certificate->file_pdf);
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
