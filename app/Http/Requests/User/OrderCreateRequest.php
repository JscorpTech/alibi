<?php

namespace App\Http\Requests\User;

use App\Enums\PaymentTypeEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'   => ['required','exists:products,id'],
            'count'        => ['required','integer','min:1','max:100'],
            'payment_type' => ['required','in:',PaymentTypeEnum::toString()],
        ];
    }
}
