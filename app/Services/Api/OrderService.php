<?php

namespace App\Services\Api;

use App\Enums\OrderStatusEnum;
use App\Http\Helpers\Helper;
use App\Models\Address;
use App\Models\Basket;
use App\Models\OrderGroup;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderService extends UserService
{
    /**
     * Create Order service function
     *
     * @throws Exception
     */
    public function create($request): void
    {
        $cashback = $request->input('cashback', 0);
        $delivery_date = $request->input('delivery_date', null);

        $delivery_date = $delivery_date == null ? $delivery_date : Carbon::parse($delivery_date);

        if (Auth::user()->balance < $cashback) {
            throw new Exception(__('Cashback yetarli emas'));
        }

        $address = Address::query()->create([
            'long'        => $request->input('address:long'),
            'lat'         => $request->input('address:lat'),
            'label'       => $request->input('address:label'),
            'region_id'   => $request->input('address:region', null),
            'district_id' => $request->input('address:district', null),
        ]); // Create address

        $order_group = OrderGroup::query()->create([
            'user_id'       => Auth::id(),
            'address_id'    => $address->id,
            'payment_type'  => $request->input('payment_type'),
            'cashback'      => $cashback,
            'delivery_date' => $delivery_date,
        ]);

        $basket = $request->input('basket');

        foreach ($basket as $item) {
            $group = Basket::query()->where('id', $item)->first();
            $product = $group->product;
            $count = $group->count; // Get order product count

            $price = Helper::clearSpace($product->getPriceNumber()); // clear price for spaces

            $response = $this->getProductPrice($price);

            $price = $response->price;
            $discount = $response->discount;

            $order_group->orders()->create([
                'product_id' => $product->id,
                'count'      => $count,
                'color_id'   => $group->color_id,
                'size_id'    => $group->size_id,

                'status'   => OrderStatusEnum::PENDING,
                'price'    => $price,
                'discount' => $discount,
            ]); // Create Order

            /**
             * Delete basket products
             *
             */
            $group->delete();
        }
    }
}
