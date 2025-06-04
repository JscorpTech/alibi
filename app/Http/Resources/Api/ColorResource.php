<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return [
            'name'  => $this->name,
            'color' => $this->color,
        ];
    }
}
