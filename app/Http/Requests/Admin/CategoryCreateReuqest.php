<?php

namespace App\Http\Requests\Admin;

use App\Enums\GenderEnum;
use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateReuqest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            ...LocaleService::getRule('name', ['required', 'string', 'max:255']),
            'position' => ['required', 'min:1', 'max:255', 'integer'],
            'gender'   => ['required', 'in:' . GenderEnum::toString()],
            'image'    => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
        ];
    }
}
