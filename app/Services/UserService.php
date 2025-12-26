<?php

namespace App\Services;

use App\Enums\LoyaltyLevelEnum;
use App\Enums\LoyaltyThresholdEnum;
use App\Enums\LoyaltyRateEnum;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Get product price - без скидки первого заказа
     *
     * @param $price
     * @return object
     */
    public function getProductPrice($price): object
    {
        // Скидок больше нет — возвращаем как есть
        return (object) [
            'price'    => (int) $price,
            'discount' => 0,
        ];
    }

    /**
     * Get user discount - скидок больше нет
     *
     * @return object
     */
    public function getDiscount(): object
    {
        return (object) [
            'discount' => 0,
            'type'     => null,
        ];
    }

    /**
     * Get Cashback percent by level
     *
     * @param $user
     * @return int
     */
    public static function getCashback($user = null): int
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return LoyaltyRateEnum::START;
        }

        $level = $user->level ?? LoyaltyLevelEnum::START;

        return match ($level) {
            LoyaltyLevelEnum::VIP    => LoyaltyRateEnum::VIP,
            LoyaltyLevelEnum::GOLD   => LoyaltyRateEnum::GOLD,
            LoyaltyLevelEnum::SILVER => LoyaltyRateEnum::SILVER,
            LoyaltyLevelEnum::BRONZE => LoyaltyRateEnum::BRONZE,
            default                  => LoyaltyRateEnum::START,
        };
    }

    /**
     * Update user level based on total_spent
     *
     * @param $user
     * @return void
     */
    public static function updateLevel($user): void
    {
        if (!$user) {
            return;
        }

        $spent = $user->total_spent ?? 0;

        $newLevel = match (true) {
            $spent >= LoyaltyThresholdEnum::VIP    => LoyaltyLevelEnum::VIP,
            $spent >= LoyaltyThresholdEnum::GOLD   => LoyaltyLevelEnum::GOLD,
            $spent >= LoyaltyThresholdEnum::SILVER => LoyaltyLevelEnum::SILVER,
            $spent >= LoyaltyThresholdEnum::BRONZE => LoyaltyLevelEnum::BRONZE,
            default                                => LoyaltyLevelEnum::START,
        };

        if (($user->level ?? LoyaltyLevelEnum::START) !== $newLevel) {
            $user->level = $newLevel;
            $user->save();
        }
    }

    /**
     * Get loyalty info for API
     *
     * @param $user
     * @return array
     */
    public static function getLoyaltyInfo($user = null): array
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return [
                'balance'     => 0,
                'level'       => LoyaltyLevelEnum::START,
                'rate'        => LoyaltyRateEnum::START,
                'total_spent' => 0,
                'next_level'  => LoyaltyLevelEnum::BRONZE,
                'remaining'   => LoyaltyThresholdEnum::BRONZE,
            ];
        }

        $level = $user->level ?? LoyaltyLevelEnum::START;
        $rate = self::getCashback($user);
        $nextLevel = self::getNextLevel($level);
        $nextThreshold = $nextLevel ? self::getThreshold($nextLevel) : null;
        $remaining = $nextThreshold ? max(0, $nextThreshold - ($user->total_spent ?? 0)) : 0;

        return [
            'balance'     => (int) ($user->balance ?? 0),
            'level'       => $level,
            'rate'        => $rate,
            'total_spent' => (int) ($user->total_spent ?? 0),
            'next_level'  => $nextLevel,
            'remaining'   => $remaining,
        ];
    }

    /**
     * Get next level
     */
    private static function getNextLevel(string $level): ?string
    {
        return match ($level) {
            LoyaltyLevelEnum::START  => LoyaltyLevelEnum::BRONZE,
            LoyaltyLevelEnum::BRONZE => LoyaltyLevelEnum::SILVER,
            LoyaltyLevelEnum::SILVER => LoyaltyLevelEnum::GOLD,
            LoyaltyLevelEnum::GOLD   => LoyaltyLevelEnum::VIP,
            default                  => null,
        };
    }

    /**
     * Get threshold for level
     */
    private static function getThreshold(string $level): int
    {
        return match ($level) {
            LoyaltyLevelEnum::BRONZE => LoyaltyThresholdEnum::BRONZE,
            LoyaltyLevelEnum::SILVER => LoyaltyThresholdEnum::SILVER,
            LoyaltyLevelEnum::GOLD   => LoyaltyThresholdEnum::GOLD,
            LoyaltyLevelEnum::VIP    => LoyaltyThresholdEnum::VIP,
            default                  => 0,
        };
    }

    /**
     * @deprecated Используй level вместо card
     */
    public static function getCard($user = null)
    {
        return null;
    }
}
