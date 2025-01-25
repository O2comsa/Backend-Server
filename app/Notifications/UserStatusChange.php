<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserStatusChange extends Notification
{
    use Queueable;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
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
            ->subject(trans('app.account_unban_notification')) // Custom subject
            ->greeting('إشعار رفع حظر الحساب في تطبيق إشارتي') // Arabic subject
            ->line('مرحباً، ' . $this->user->name) // Include the user name dynamically
            ->line('نود إعلامكم بأنه تم رفع الحظر عن حسابكم في تطبيق إشارتي.')
            ->line('نشكركم على تفهمكم، ونتمنى لكم كل التوفيق.')
            ->salutation('مع خالص التحية،'); // Add a closing message
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
