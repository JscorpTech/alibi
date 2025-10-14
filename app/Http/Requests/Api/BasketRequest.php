<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => [
                'required',
                'integer',
                Rule::exists('variants', 'id')
                    ->where(fn($q) => $q->where('product_id', $this->input('product_id')))
            ],
            'count' => ['required', 'integer', 'min:1'],
        ];
    }
}