<?php

namespace App\Notifications\Zoom;

use App\Models\ZoomMeeting;
use Illuminate\Notifications\Notification;

class MeetingInvite extends Notification
{
    private $meeting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ZoomMeeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];

        if ($notifiable->email_notifications) {
            array_push($via, 'mail');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     * @throws \Exception
     */
    public function toMail($notifiable)
    {
        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');
        $vEvent = new \Eluceo\iCal\Component\Event;
        $vEvent
            ->setDtStart(new \DateTime($this->meeting->start_date_time))
            ->setDtEnd(new \DateTime($this->meeting->end_date_time))
            ->setNoTime(true)
            ->setSummary(ucfirst($this->meeting->meeting_name));
        $vCalendar->addComponent($vEvent);
        $vFile = $vCalendar->render();

        $url = route('login');

        return parent::build()
            ->subject(__('zoom::email.newMeeting.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('zoom::email.newMeeting.text'))
            ->line(__('zoom::modules.zoommeeting.meetingName') . ': ' . $this->meeting->meeting_name)
            ->line(__('zoom::modules.zoommeeting.startOn') . ': ' . $this->meeting->start_date_time)
            ->line(__('zoom::modules.zoommeeting.endOn') . ': ' . $this->meeting->end_date_time)
            ->action(__('email.loginDashboard'), $url)
            ->line(__('email.thankyouNote'))
            ->attachData($vFile, 'cal.ics', [
                'mime' => 'text/calendar',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    // phpcs:ignore
    public function toArray($notifiable)
    {
        return [
            'id' => $this->meeting->id,
            'start_date_time' => $this->meeting->start_date_time->format('Y-m-d H:i:s'),
            'meeting_name' => $this->meeting->meeting_name,
        ];
    }
}
