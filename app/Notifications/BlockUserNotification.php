<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BlockUserNotification extends Notification
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
            ->subject('إشعار حظر الحساب في تطبيق إشارتي')
            ->greeting('السلام عليكم ورحمة الله وبركاته،')
            ->line('نود إعلامكم بأنه تم حظر حسابكم في [تطبيق إشارتي].')
            ->line('وقد تم اتخاذ هذا القرار وفقًا للسياسات المعتمدة لدينا. نحثكم على مراجعة شروط الاستخدام الخاصة بنا للتأكد من الالتزام بها في المستقبل.')
            ->line('إذا كان لديكم أي استفسارات أو ترغبون في مناقشة هذا القرار، يمكنكم التواصل مع فريق الدعم لدينا عبر [ايقونة تواصل معنا في تطبيق اشارتي].')
            ->line('موضح فيها طريقة التواصل:')
            ->line('الاسم: '.$this->user->name)
            ->line('الإيميل: '.$this->user->email)
            ->line('رقم الجوال: '.$this->user->mobile) // تأكد من وجود حقل الهاتف في النموذج
            ->line('نشكركم على تفهمكم، ونتمنى لكم كل التوفيق.')
            ->line('مع خالص التحية،')
            ->line('جمعية مترجمي لغة الإشارة (إشارتي)');
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
