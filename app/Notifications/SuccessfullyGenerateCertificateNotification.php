<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\LiveEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class SuccessfullyGenerateCertificateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $title;
    private array $data = [];
    private string $body;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Certificate $certificate)
    {
        $this->title = '';
        $this->body = '';

        if ($this->certificate->related instanceof LiveEvent) {
            $this->title = 'تم إصدار الشهادة';
            $this->body = "تم إصدار الشهادة لحضورك الندوة المباشرة : {$this->certificate->related->name} ";
        } elseif ($this->certificate->related instanceof Course) {
            $this->title = 'تم إصدار الشهادة';
            $this->body = "تم إصدار الشهادة لإتمامك الدورة : {$this->certificate->related->title} ";
        }

        $this->data = [
            'title' => $this->title,
            'body' => $this->body,
            'type' => 'certificate',
            'id' => $this->certificate->related_id
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels[] = 'database';

        if ($notifiable->device_token) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData($this->data)
            ->setNotification(
                \NotificationChannels\Fcm\Resources\Notification::create()
                    ->setTitle($this->title)
                    ->setBody($this->body)
                    ->setImage(asset('frontend/images/fav.png'))
            );
    }

    // optional method when using kreait/laravel-firebase:^3.0, this method can be omitted, defaults to the default project
    public function fcmProject($notifiable, $message)
    {
        // $message is what is returned by `toFcm`
        return 'app'; // name of the firebase project to use
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->data;
    }

    /**
     * Get the notification's database type.
     *
     * @param object $notifiable
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'certificate-firebase';
    }
}
