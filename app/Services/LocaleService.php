<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class LocaleService
{
    public static function getLocaleFields(array|string $keys, array $ignore = []): array
    {
        $fields = [];
        $keys = BaseService::ifStringToArray($keys);
        foreach ($keys as $key) {
            if (in_array($key, $ignore)) {
                $fields[] = $key;
                continue;
            }

            foreach (Config::get('app.locales') as $locale) {
                $fields[] = $key . '_' . $locale;
            }
        }

        return $fields;
    }

    public static function getRule(array|string $field, array|string $rule): array
    {
        $rules = [];
        $fields = self::getLocaleFields($field);

        foreach ($fields as $key) {
            $rules[$key] = $rule;
        }

        return $rules;
    }

    public static function getMigration($table, $func, array|string $fields): array
    {
        $response = [];
        $fields = self::getLocaleFields($fields);
        foreach ($fields as $field) {
            $response[] = $table->$func($field);
        }

        return $response;
    }
}
