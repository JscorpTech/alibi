<?php

namespace App\Services\Api;

use App\Enums\OrderStatusEnum;
use App\Models\Address;
use App\Models\Basket;
use App\Models\OrderGroup;
use App\Models\Variant;
use App\Services\UserService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService extends UserService
{
    /**
     * Создание заказа (только variant_id)
     *
     * @throws Exception
     */
    public function create($request): void
    {
        $cashback = (int) $request->input('cashback', 0);
        $deliveryRaw = $request->input('delivery_date');
        $delivery = $deliveryRaw ? Carbon::parse($deliveryRaw) : null;

        if ((int) Auth::user()->balance < $cashback) {
            throw new Exception(__('Cashback yetarli emas'));
        }

        DB::transaction(function () use ($request, $cashback, $delivery) {

            $address = Address::query()->create([
                'long' => $request->input('address:long'),
                'lat' => $request->input('address:lat'),
                'label' => $request->input('address:label'),
                'region_id' => $request->input('address:region'),
                'district_id' => $request->input('address:district'),
            ]);

            $orderGroup = OrderGroup::query()->create([
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'payment_type' => $request->input('payment_type'),
                'cashback' => $cashback,
                'delivery_date' => $delivery,
                'source' => 'app',
                'type' => 'sale',
            ]);

            $basketIds = (array) $request->input('basket', []);
            if (empty($basketIds)) {
                throw new Exception(__('Savatcha bo‘sh'));
            }

            $rows = Basket::query()
                ->with('product:id,price')
                ->whereIn('id', $basketIds)
                ->get();

            if ($rows->isEmpty()) {
                throw new Exception(__('Savatcha bo‘sh'));
            }

            // ⭐ аккумулируем сумму чека
            $groupTotal = 0;

            $grouped = $rows->groupBy('variant_id')->map(function ($items) {
                /** @var Basket $first */
                $first = $items->first();
                return [
                    'row' => $first,
                    'count_sum' => (int) $items->sum('count'),
                    'ids' => $items->pluck('id')->all(),
                ];
            });

            foreach ($grouped as $variantId => $pack) {
                /** @var Basket $row */
                $row = $pack['row'];
                $count = $pack['count_sum'];

                if (empty($variantId)) {
                    throw new Exception(__('Variant tanlanmagan'));
                }

                /** @var Variant|null $variant */
                $variant = Variant::query()
                    ->where('id', $variantId)
                    ->where('product_id', $row->product_id)
                    ->first();

                if (!$variant) {
                    throw new Exception(__('Variant topilmadi'));
                }

                if ($count <= 0) {
                    Basket::query()->whereIn('id', $pack['ids'])->delete();
                    continue;
                }

                $productPrice = (int) ($row->product?->price ?? 0);
                $finalPrice = (int) ($variant->price ?? 0);
                if ($finalPrice <= 0) {
                    $finalPrice = $productPrice;
                }
                $discount = 0;

                // списываем остаток
                $affected = Variant::query()
                    ->where('id', $variant->id)
                    ->where('stock', '>=', $count)
                    ->decrement('stock', $count);

                if ($affected === 0) {
                    throw new Exception(__('Mahsulot yetarli emas'));
                }

                $orderGroup->orders()->create([
                    'product_id' => $row->product_id,
                    'variant_id' => $variant->id,
                    'count' => $count,
                    'status' => OrderStatusEnum::PENDING,
                    'price' => $finalPrice,
                    'discount' => $discount,
                ]);

                // ⭐ плюсуем строку в итог (цена минус скидка * количество)
                $groupTotal += max(0, (int) $finalPrice - (int) $discount) * (int) $count;

                // чистим строки корзины
                Basket::query()->whereIn('id', $pack['ids'])->delete();
            }

            // ⭐ после цикла сохраняем total в группе
            $orderGroup->update([
                'total' => (int) $groupTotal,
            ]);
        });
    }
}