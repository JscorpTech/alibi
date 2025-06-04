<?php

namespace App\Models;

use App\Services\LocaleService;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

trait BaseModel
{
    public function __construct(array $attributes = [])
    {
        if (method_exists($this, 'localeFields')) {
            foreach (LocaleService::getLocaleFields($this->localeFields()) as $field) {
                $this->fillable[] = $field;
            }
        }
        parent::__construct($attributes);
    }

    /**
     * @throws Exception
     */
    public static function findOrField($id, $message = '', $error = null, $code = 404)
    {
        $error = $error ?? __('not:found');

        $class = parent::find($id);

        $message = $message != '' ? $message . '|' . $error : $error;
        if (!$class) {
            abort(404, $message);
        }

        return $class;
    }

    public function getAttribute($key)
    {
        $lang = App::currentLocale() ?? 'uz';
        if (!in_array($lang, Config::get('app.locales'))) {
            $lang = 'uz';
        }

        if (method_exists($this, 'jsonFields') and in_array($key, $this->jsonFields())) {
            $data = parent::getAttribute($key);
            while (true) {
                try {
                    if (!is_string($data)) {
                        return $data;
                    }
                    $data = json_decode($data);
                } catch (\Throwable $e) {
                    return $data;
                }
            }
        }

        if (method_exists($this, 'isNullToZero') and in_array($key, $this->isNullToZero())) {
            $data = parent::getAttribute($key);
            if ($data == null) {
                return 0;
            }
        }

        if (method_exists($this, 'localeFields') and in_array($key, $this->localeFields())) {
            $data = parent::getAttribute($key . '_' . $lang);
            if (method_exists($this, 'hashFields') and in_array($key . '_' . $lang, $this->hashFields())) {
                $data = base64_decode($data);
            }

            return $data;
        }
        $data = parent::getAttribute($key);
        if (method_exists($this, 'hashFields') and in_array($key, $this->hashFields())) {
            $data = base64_decode($data);
        }

        return $data;
    }
}
