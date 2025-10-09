<?php

namespace App\Services\Sales;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    /**
     * Создание чека-возврата по исходному заказу.
     *
     * @param array{
     *   original_group_id:int,
     *   user_id?:int|null,
     *   cashier_id?:int|null,
     *   payment_method?:string|null,   // например 'refund' | 'cash' | 'card'
     *   comment?:string|null,
     *   lines: array<int, array{
     *      order_id:int,
     *      product_id:int,
     *      price:int,                  // цена в исходном чеке (за единицу)
     *      count:int,                  // сколько вернуть (шт)
     *      size_id?:int|null,
     *      color_id?:int|null,
     *   }>,
     * } $payload
     */
    // app/Services/Sales/ReturnService.php

    public function create(array $payload): OrderGroup
    {
        return DB::transaction(function () use ($payload) {

            // Берём исходный чек и fallback для user_id
            $original = OrderGroup::findOrFail((int) $payload['original_group_id']);

            $userId = $payload['user_id']
                ?? $original->user_id
                ?? (int) env('POS_GUEST_USER_ID', 1); // <- задай .env, см. ниже

            // 1) создаём группу для возврата
            $group = OrderGroup::create([
                'user_id' => $userId,                // <= НЕ null
                'status' => OrderStatusEnum::SUCCESS,
                'source' => 'pos',
                'cashier_id' => $payload['cashier_id'] ?? null,
                'payment_method' => $payload['payment_method'] ?? 'refund',
                'comment' => trim(($payload['comment'] ?? '') . ' (возврат по чеку #' . $original->id . ')'),
                'paid_at' => now(),
                // если нужен склад в возврате, добавь:
                // 'location_id'    => $payload['location_id'] ?? $original->location_id,
            ]);

            $total = 0;

            foreach ($payload['lines'] as $line) {
                $count = (int) ($line['count'] ?? 0);
                if ($count <= 0)
                    continue;

                $price = (int) $line['price'];
                $sum = $price * $count;

                Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $userId,
                    'product_id' => (int) $line['product_id'],
                    'size_id' => $line['size_id'] ?? null,
                    'color_id' => $line['color_id'] ?? null,
                    'price' => $price,
                    'discount' => 0,
                    'count' => $count,
                ]);

                if (!empty($line['size_id'])) {
                    $ps = ProductSize::where('product_id', $line['product_id'])
                        ->where('size_id', $line['size_id'])
                        ->lockForUpdate()
                        ->first();

                    if ($ps) {
                        $ps->increment('count', $count); // вернули на склад
                    }
                }

                $total += $sum;
            }

            $group->update([
                'total' => -abs($total), // возврат — отрицательная сумма
                'order_number' => now()->format('YmdHis') . $group->id,
            ]);

            return $group;
        });
    }
}