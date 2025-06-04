<?php

namespace App\Http\Requests\Api;

use App\Enums\PaymentTypeEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address:long'     => ['required'],
            'address:lat'      => ['required'],
            'payment_type'     => ['required', 'in:' . PaymentTypeEnum::toString()],
            'cashback'         => ['integer'],
            'delivery_date'    => ['date_format:Y-m-d'],
            'address:label'    => ['required', 'string', 'max:255'],
            'address:region'   => ['integer', 'exists:regions,id'],
            'address:district' => ['integer', 'exists:districts,id'],

            'basket'   => ['required', 'array'],
            'basket.*' => ['required', 'integer', 'exists:baskets,id'],
        ];
    }
}
