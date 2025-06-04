<?php

namespace App\Models;

use App\Services\CacheService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    use BaseModel;

    public $fillable = [
        'key',
        'value',
        'label',
    ];

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public static function get($key, $default = null): mixed
    {
        return CacheService::remember(function () use ($key, $default) {
            $data = self::query()->where(['key' => $key]);
            if ($data->exists()) {
                return $data->first()->value;
            } else {
                return $default;
            }

            return $data;
        }, key: md5('settings:' . $key));
    }
}
