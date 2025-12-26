<?php

namespace App\Enums;

class LoyaltyLevelEnum
{
    public const START = 'start';
    public const BRONZE = 'bronze';
    public const SILVER = 'silver';
    public const GOLD = 'gold';
    public const VIP = 'vip';
    
    public static function all(): array
    {
        return [
            self::START,
            self::BRONZE,
            self::SILVER,
            self::GOLD,
            self::VIP,
        ];
    }
    
    public static function labels(): array
    {
        return [
            self::START => 'Start',
            self::BRONZE => 'Bronze',
            self::SILVER => 'Silver',
            self::GOLD => 'Gold',
            self::VIP => 'VIP',
        ];
    }
}
