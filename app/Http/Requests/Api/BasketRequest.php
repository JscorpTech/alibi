<?php

namespace App\Http\Requests\Api;

use App\Enums\PaymentTypeEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class BasketRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'count'      => ['required','integer', 'min:1', 'max:100'],
            'color_id'   => ['required', 'exists:colors,id'],
            'size_id'    => ['required', 'exists:sizes,id'],
        ];
    }
}
