<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\GenderEnum;
use App\Enums\ProductStatusEnum;
use App\Http\Requests\BaseRequest;
use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'gender'      => ['required', 'in:' . GenderEnum::toString()],
            'price'       => ['required', 'max:255'],
            'discount'    => ['max:255'],
            'counts'      => ['required','array','max:100','min:1'],
            'status'      => ['required', 'in:' . ProductStatusEnum::toString()],
            'sizes'       => ['required','min:1','array'],
            'colors'      => ['required','min:1','array'],
        ];
    }
}
