<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Dictionary;
use App\Models\LiveEvent;
use App\Models\Paytabs;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class SuccessfullyPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $title;
    private array $data = [];
    private string $body;
    private bool $shouldSend = false;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Paytabs $paytabs)
    {
        if ($this->paytabs->paid == 1) {
            $this->shouldSend = true;

            if ($this->paytabs->related instanceof Course) {
                $this->title = 'تم اتمام عملية شراء الدورة بنجاح';
                $this->body = "تم اتمام شراء دورة {$this->paytabs->related->title}";
                $this->data = [
                    'title' => $this->title,
                    'body' => $this->body,
                    'type' => 'payment',
                    'related_type' => 'course',
                    'related_id' => "{$this->paytabs->related_id}"
                ];
            } elseif ($this->paytabs->related instanceof LiveEvent) {
                $this->title = 'تم اتمام عملية شراء الندوة المباشرة بنجاح';
                $this->body = "تم اتمام شراء دورة {$this->paytabs->related->name}";
                $this->data = [
                    'title' => $this->title,
                    'body' => $this->body,
                    'type' => 'payment',
                    'related_type' => 'live-event',
                    'related_id' => "{$this->paytabs->related_id}"
                ];
            } else if ($this->paytabs->related instanceof Dictionary) {
                $this->title = 'تم اتمام عملية شراء القاموس بنجاح';
                $this->body = "تم اتمام شراء دورة {$this->paytabs->related->title}";
                $this->data = [
                    'title' => $this->title,
                    'body' => $this->body,
                    'type' => 'payment',
                    'related_type' => 'dictionary',
                    'related_id' => "{$this->paytabs->related_id}"
                ];
            } else if ($this->paytabs->related instanceof Plan) {
                $this->title = 'تم اتمام عملية شراء الباقة بنجاح';
                $this->body = "تم اتمام شراء دورة {$this->paytabs->related->name}";
                $this->data = [
                    'title' => $this->title,
                    'body' => $this->body,
                    'type' => 'payment',
                    'related_type' => 'plan',
                    'related_id' => "{$this->paytabs->related_id}"
                ];
            }
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (!$this->shouldSend) {
            return [];
        }

        $channels[] = 'database';

        if ($notifiable->device_token) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function toFcm($notifiable)
    {
        if (!$this->shouldSend) {
            return null;
        }

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
    public function fcmProject($notifiable, $message): string
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
        if (!$this->shouldSend) {
            return [];
        }

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
        if (!$this->shouldSend) {
            return '';
        }

        return 'payment-firebase';
    }
}
