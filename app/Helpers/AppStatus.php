<?php
// Copyright
declare(strict_types=1);


namespace App\Helpers;

class AppStatus
{
    const UNCONFIRMED = 'unconfirmed';
    const ACTIVE = 'active';
    const BANNED = 'banned';

    public static function lists()
    {
        return [
            self::ACTIVE => trans('app.'.self::ACTIVE),
            self::BANNED => trans('app.'. self::BANNED),
            self::UNCONFIRMED => trans('app.' . self::UNCONFIRMED)
        ];
    }
}
