<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class EditStatusRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:' . OrderStatusEnum::toString()],
        ];
    }
}
