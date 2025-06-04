<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
