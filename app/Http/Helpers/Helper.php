<?php

namespace App\Http\Helpers;

class Helper
{
    /**
     * @param ...$roles
     * @return string
     * User rollarini middlewarega tayyorlab berish uchun
     */
    public static function roles(...$roles): string
    {
        return 'role:' . implode(',', $roles);
    }

    /**
     * @param array $data
     * @return array
     * Null elementlarni olib tashlash uchun
     */
    public static function removeNullData(array $data): array
    {
        return array_filter($data, function ($arr) {
            return $arr !== null;
        });
    }

    public static function count($data): array
    {
        $response = [];

        $count = 1;
        foreach ($data as $key => $datum) {
            if (isset($data[$key + 1]) and $datum == $data[$key + 1]) {
                $count += 1;
            } else {
                $response[] = [
                    'data'  => $datum,
                    'count' => $count,
                ];
                $count = 1;
            }
        }

        return $response;
    }

    public static function clearPhone($value)
    {
        $l = [
            '(',
            ')',
            '-',
            ' ',
        ];
        foreach ($l as $item) {
            $value = str_replace($item, '', $value);
        }

        return $value;
    }

    public static function clearSpace($text): string
    {
        return str_replace(' ', '', $text);
    }

    public static function isError($errors, $el): string
    {
        if ($errors->has($el)) {
            return 'is-invalid';
        } elseif ($errors->any()) {
            return 'is-valid';
        } else {
            return '';
        }
    }

    public static function checkPhone($phone): bool
    {
        if (preg_match('/^(998)(90|91|92|93|94|95|96|97|98|99|33|88|55)[0-9]{7}$/', $phone)) {
            return true;
        }

        return false;
    }
}
