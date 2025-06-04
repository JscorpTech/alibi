<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\GenderEnum;
use App\Enums\ProductStatusEnum;
use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            ...LocaleService::getRule('name', ['required', 'max:255']),
            ...LocaleService::getRule('desc', ['required']),
            'gender'     => ['required', 'in:' . GenderEnum::toString()],
            'price'      => ['required', 'max:255'],
            'discount'   => ['max:255'],
            'status'     => ['required', 'in:' . ProductStatusEnum::toString()],
            'size-image' => ['required', 'exists:size_infos,id'],
        ];
    }
}
