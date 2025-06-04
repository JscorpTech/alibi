<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeResource extends JsonResource
{
    use BaseResource;

    public function toArray(Request $request): array
    {
        $service = new UserService();

        return [
            'full_name'      => $this->full_name,
            'phone'          => $this->phone,
            'is_first_order' => $this->is_first_order ?? false, // is first order
            'balance'        => number_format($this->balance, 2),
            'card'           => [
                'cashback' => UserService::getCashback($this),
                'name'     => __(UserService::getCard($this)),
            ],
            'discount'   => $service->getDiscount(),
            'created_at' => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}
