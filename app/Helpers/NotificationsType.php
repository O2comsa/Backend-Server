<?php


namespace App\Helpers;


class NotificationsType
{
    const general = 1;
    const reminder = 2;
    const bank = 3;
    const payment_online = 4;

    public static function getString($index)
    {
        $index--;
        $const = array(
            'general', // 1
            'reminder', // 2
            'bank', // 3
            'payment_online', // 4
        );
        return $const[$index];
    }

    public static function getIndex($string)
    {
        $const = array(
            'general', // 1
            'reminder', // 2
            'bank', // 3
            'payment_online', // 4
        );
        return (array_search($string, $const) + 1);
    }
}
