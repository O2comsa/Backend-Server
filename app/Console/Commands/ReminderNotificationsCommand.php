<?php

namespace App\Console\Commands;

use App\Helpers\NotificationsType;
use App\Http\Controllers\Admin\PushNotifications;
use App\Models\Notifications;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReminderNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Reminder Notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $subscription = Subscription::where('expired_in', '>', Carbon::now())
            ->whereHas('user', function ($sql) {
                $sql->whereDoesntHave('notifications', function ($sql2) use ($sql) {
                    $sql2->where('type', NotificationsType::reminder)->where('created_at', '<', Carbon::now());
                });
            })
            ->with(['user'])
            ->get();
        $subscription_expire = $subscription->where('notifications_period', '>=', 'expired_after');

        foreach ($subscription_expire as $subscription) {
            if (isset($subscription->user->device_token)) {
                PushNotifications::sendMessage(trans('app.reminder_notifications', ['days' => $subscription->expired_after]), $subscription->user->device_token);
            }
            Notifications::create([
                'user_id' => $subscription->user->id, 'text' => trans('app.reminder_notifications', ['days' => $subscription->expired_after]), 'type' => NotificationsType::reminder, 'data' => null, 'seen' => 0
            ]);
        }
    }
}
