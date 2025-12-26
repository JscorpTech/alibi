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
        $loyalty = UserService::getLoyaltyInfo($this->resource);

        return [
            'full_name'      => $this->full_name,
            'phone'          => $this->phone,
            'balance'        => (int) ($this->balance ?? 0),
            'level'          => $loyalty['level'],
            'total_spent'    => $loyalty['total_spent'],
            'loyalty'        => [
                'level'       => $loyalty['level'],
                'rate'        => $loyalty['rate'],
                'balance'     => $loyalty['balance'],
                'total_spent' => $loyalty['total_spent'],
                'next_level'  => $loyalty['next_level'],
                'remaining'   => $loyalty['remaining'],
            ],
            'created_at'     => $this->created_at->format('d.m.Y H:i'),
        ];
    }
}
