<?php

namespace App\Notifications\Zoom;

use Illuminate\Notifications\Messages\SlackMessage;
use App\Models\ZoomMeeting;
use App\Models\ZoomNotificationSetting;
use Illuminate\Notifications\Notification;

class InviteHost extends Notification
{
    private $meeting;

    private $emailSetting;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ZoomMeeting $meeting)
    {
        $this->meeting = $meeting;
        $this->emailSetting = ZoomNotificationSetting::where('slug', 'zoom-meeting-created')->first();
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

        if ($this->emailSetting->send_email == 'yes' && $notifiable->email_notifications && $notifiable->email != '') {
            array_push($via, 'mail');
        }

        if ($this->emailSetting->send_slack == 'yes') {
            array_push($via, 'slack');
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
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

        $emailContent = parent::build()
            ->subject(__('zoom::email.newMeeting.subject') . ' - ' . config('app.name'))
            ->greeting(__('email.hello') . ' ' . $notifiable->name . '!')
            ->line(__('zoom::email.newMeeting.text'))
            ->line(__('zoom::modules.zoommeeting.meetingName') . ': ' . $this->meeting->meeting_name)
            ->line(__('zoom::modules.zoommeeting.startOn') . ': ' . $this->meeting->start_date_time->format($this->company->date_format . ' - ' . $this->company->time_format))
            ->line(__('zoom::modules.zoommeeting.endOn') . ': ' . $this->meeting->end_date_time->format($this->company->date_format . ' - ' . $this->company->time_format))
            ->line(__('zoom::modules.meetings.meetingId') . ': ' . $this->meeting->meeting_id);

        $emailContent = $emailContent->line(__('zoom::modules.zoommeeting.meetingPassword') . ' - ' . $this->meeting->password);
        $emailContent = $emailContent->action(__('zoom::modules.zoommeeting.startUrl'), url($this->meeting->start_link));

        return $emailContent->line(__('zoom::email.thankyouNote'));
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
            'user_id' => $notifiable->id,
            'start_date_time' => $this->meeting->start_date_time->format('Y-m-d H:i:s'),
            'meeting_name' => $this->meeting->meeting_name,
        ];
    }

    public function toSlack($notifiable)
    {

        $slack = $notifiable->company->slackSetting;

        if (count($notifiable->employee) > 0 && (!is_null($notifiable->employee[0]->slack_username) && ($notifiable->employee[0]->slack_username != ''))) {
            return (new SlackMessage)
                ->from(config('app.name'))
                ->image($slack->slack_logo_url)
                ->to('@' . $notifiable->employee[0]->slack_username)
                ->content(__('zoom::email.newMeeting.subject') . ' - ' . $this->meeting->meeting_name . "\n" . url('/login'));
        }

        return (new SlackMessage)
            ->from(config('app.name'))
            ->image($slack->slack_logo_url)
            ->content('*' . __('zoom::email.newMeeting.subject') . '*' . "\n" . 'This is a redirected notification. Add slack username for *' . $notifiable->name . '*');
    }
}
