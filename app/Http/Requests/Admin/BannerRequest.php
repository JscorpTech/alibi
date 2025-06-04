<?php

namespace App\Http\Requests\Admin;

use App\Enums\BannerEnum;
use App\Enums\BannerStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    use BaseRequest;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'link'     => ['required', 'string', 'max:255', 'url'],
            'image'    => ['required', 'file'],
            'position' => ['required', 'in,' . BannerEnum::toString()],
            'status'   => ['required', 'in,' . BannerStatusEnum::toString()],
        ];
    }
}
