<?php

// app/Support/Barcode.php
namespace App\Support;

class Barcode
{
    public static function makeEan13(): string
    {
        $base = '';
        for ($i = 0; $i < 12; $i++) {
            $base .= (string) random_int(0, 9);
        }
        return $base . self::ean13Checksum($base);
    }

    public static function isValid(?string $code): bool
    {
        if (!$code || !ctype_digit($code) || strlen($code) !== 13)
            return false;
        $body = substr($code, 0, 12);
        $check = (int) $code[12];
        return self::ean13Checksum($body) === $check;
    }

    protected static function ean13Checksum(string $digits12): int
    {
        $sum = 0;
        foreach (str_split($digits12) as $i => $d) {
            $n = (int) $d;
            $sum += ($i % 2 === 0) ? $n : $n * 3;
        }
        $m = $sum % 10;
        return $m === 0 ? 0 : (10 - $m);
    }
}
