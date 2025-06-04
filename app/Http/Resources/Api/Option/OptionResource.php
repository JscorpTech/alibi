<?php

namespace App\Http\Resources\Api\Option;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
{
    use BaseResource;


    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "items" => ItemResource::collection($this->items ?? []),
        ];
    }
}
