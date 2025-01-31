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
        // $expired = DB::table('live_event_attendees')
        //     ->where('is_confirmed', false)->orWhere('is_confirmed', 0)
        //     ->where('reserved_at', '<', Carbon::now()->subMinutes(1)) // وقت الحجز 1 دقائق
        //     ->delete();


        $expiredReservations = DB::table('live_event_attendees')
            ->where('is_confirmed', 0)
            ->where('reserved_at', '<', now()->subMinutes(1)) // Reservations older than 15 minutes
            ->get();

        foreach ($expiredReservations as $reservation) {
            // Decrement the `reserved_seats` column for the corresponding event
            DB::table('live_events')
                ->where('id', $reservation->live_event_id)
                ->decrement('reserved_seats');

            // Delete the expired reservation
            DB::table('live_event_attendees')
                ->where('id', $reservation->id)
                ->delete();

            $this->info("تم تحرير المقاعد المحجوزة المنتهية: {$reservation} سجل");
        }
    }
}
