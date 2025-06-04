<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class IsAlreadyRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'color_id' => [],
            'size_id'  => [],
        ];
    }
}
