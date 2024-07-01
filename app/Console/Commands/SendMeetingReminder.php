<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ZoomMeeting;
use App\Events\Zoom\MeetingReminderEvent;

class SendMeetingReminder extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'send-zoom-meeting-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send meeting reminder to the attendees before time specified in database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = ZoomMeeting::query()
            ->select('id', 'meeting_name', 'label_color', 'description', 'start_date_time', 'end_date_time', 'repeat', 'send_reminder', 'remind_time', 'remind_type')
            ->where('start_date_time', '>=', Carbon::now())
            ->where('send_reminder', 1)
            ->get();

        foreach ($events as $event) {
            $reminderDateTime = $this->calculateReminderDateTime($event);

            if ($reminderDateTime->equalTo(now()->startOfMinute())) {
                event(new MeetingReminderEvent($event));
            }
        }
    }

    public function calculateReminderDateTime(ZoomMeeting $event)
    {
        $time = $event->remind_time;
        $type = $event->remind_type;

        $reminderDateTime = '';

        switch ($type) {
            case 'day':
                $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date_time)->subDays($time);
                break;
            case 'hour':
                $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date_time)->subHours($time);
                break;
            case 'minute':
                $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date_time)->subMinutes($time);
                break;
        }

        return $reminderDateTime;
    }
}
