<?php

namespace App\Notifications\Zoom;

use App\Notifications\BaseNotification;
use App\Models\ZoomMeeting;
use Illuminate\Notifications\Notification;

class MeetingReminder extends Notification
{
    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ZoomMeeting $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = [];

        if ($notifiable->email_notifications) {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('login');

        return parent::build()
            ->subject(__('zoom::email.meetingReminder.subject').' - '.config('app.name'))
            ->greeting(__('zoom::email.hello').' '.$notifiable->name.'!')
            ->line(__('zoom::email.meetingReminder.text'))
            ->line(__('zoom::modules.zoommeeting.meetingName').': '.$this->event->meeting_name)
            ->line(__('zoom::app.meetings.time').': '.$this->event->start_date_time->toDayDateTimeString())
            ->action(__('zoom::email.loginDashboard'), $url)
            ->line(__('zoom::email.thankyouNote'));
    }
}
