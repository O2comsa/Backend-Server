<?php

namespace App\Helpers;

class LiveSupportRequestStatus
{
    public const WAITING_STATUS = 'waiting';

    public const IN_PROGRESS_STATUS = 'in-progress';

    public const ACCEPTED_STATUS = 'accepted';

    public const COMPLETED_STATUS = 'completed';

    public const CANCELED_STATUS = 'canceled';

    public const EXPIRED_STATUS = 'expired';

    public static function lists()
    {
        return [
            self::WAITING_STATUS => trans('app.' . self::WAITING_STATUS),
            self::ACCEPTED_STATUS => trans('app.' . self::ACCEPTED_STATUS),
            self::IN_PROGRESS_STATUS => trans('app.' . self::IN_PROGRESS_STATUS),
            self::COMPLETED_STATUS => trans('app.' . self::COMPLETED_STATUS),
        ];
    }

    public static function dashbaordLists()
    {
        return [
            self::ACCEPTED_STATUS => trans('app.' . self::ACCEPTED_STATUS),
            self::CANCELED_STATUS => trans('app.' . self::CANCELED_STATUS),
        ];
    }
}
