<?php

namespace App\Support\Helpers;

class DateHelper
{
    public static function getDate($date): string
    {
        return $date->format('d M H:i');
    }
}
