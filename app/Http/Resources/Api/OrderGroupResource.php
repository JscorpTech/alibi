<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderGroupResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'payment_type'  => $this->payment_type,
            'address'       => AddressResource::make($this->address),
            'items'         => OrderResource::collection($this->orders),
            'status'        => $this->status,
            'price'         => (int) $this->getTotalPrice(),
            'delivery_date' => $this->delivery_date,
            'cashback'      => $this->cashback,
            'created_at'    => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
