<?php

namespace App\Http\Requests\Filters;

use App\Enums\OrderStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrdersFilter extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['string', 'in:all,' . OrderStatusEnum::toString()],
        ];
    }
}
