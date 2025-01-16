<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReleaseExpiredReservations extends Command
{
    // اسم الأمر
    protected $signature = 'event:release-expired-reservations';

    // وصف الأمر
    protected $description = 'Release expired event reservations that are not confirmed';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // حذف الحجوزات المنتهية والتي لم يتم تأكيدها
        $expired = DB::table('live_event_attendees')
            ->where('is_confirmed', false)
            ->where('reserved_at', '<', Carbon::now()->subMinutes(1)) // وقت الحجز 1 دقائق
            ->delete();

        $this->info("تم تحرير المقاعد المحجوزة المنتهية: {$expired} سجل");
    }
}
