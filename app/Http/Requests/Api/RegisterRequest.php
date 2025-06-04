<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            /**
             * @example 998943990509
             */
            'phone'     => ['required', new PhoneRule(), 'integer'],
            'full_name' => ['required', 'min:2', 'max:255'],
            'password'  => ['required', 'min:8', 'max:255'],
            'fcm_token' => ['string'],
        ];
    }
}
