<?php

namespace App\Enums;

class BaseEnum
{
    public static function toArray(): array
    {
        $class = new \ReflectionClass(static::class);

        return array_values($class->getConstants());
    }

    public static function toString(): string
    {
        return implode(',', static::toArray());
    }

    public static function toObject(): array
    {
        $class = new \ReflectionClass(static::class);

        return array_flip($class->getConstants());
    }
}
