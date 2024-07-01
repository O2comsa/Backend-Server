<?php
/**
 * Created by PhpStorm.
 * Developer: Tariq Ayman ( tariq.ayman94@gmail.com )
 * Date: 1/5/20, 1:52 AM
 * Last Modified: 1/5/20, 1:22 AM
 * File Name: DictionaryStatus.php
 */

declare(strict_types=1);

namespace App\Helpers;

class EventStatus
{
    const ACTIVE = 'active';
    const DISABLED = 'disabled';

    public static function lists()
    {
        return [
            self::ACTIVE => trans('app.'.self::ACTIVE),
            self::DISABLED => trans('app.'. self::DISABLED),
        ];
    }
}
