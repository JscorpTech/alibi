<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            ...LocaleService::getRule('name', ['string', 'max:255']),
            'position' => ['min:1', 'max:255', 'integer'],
            'gender'   => ['in:' . GenderEnum::toString()],
            'image'    => ['file','mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
