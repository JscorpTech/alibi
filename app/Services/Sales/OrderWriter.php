<?php

namespace App\Services\Sales;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

class OrderWriter
{
    /**
     * Создаёт OrderGroup + Orders и списывает остатки.
     *
     * @param array{
     *   user_id?: int|null,
     *   items: array<int, array{
     *     product_id:int,
     *     size_id?:int|null,
     *     color_id?:int|null,
     *     count:int,
     *     price:int,
     *     discount?:int|null
     *   }>,
     *   source: 'app'|'pos',
     *   cashier_id?: int|null,
     *   payment_method?: string|null,
     *   comment?: string|null,
     *   location_id?: int|null
     * } $payload
     */

    // App/Services/Sales/OrderWriter.php

    public function create(array $payload): OrderGroup
    {
        return DB::transaction(function () use ($payload) {

            $type = $payload['type'] ?? 'sale';
            $source = $payload['source'] ?? 'pos';

            $group = OrderGroup::create([
                'user_id' => $payload['user_id'] ?? null,
                'status' => OrderStatusEnum::PENDING,
                'source' => $source,
                'cashier_id' => $payload['cashier_id'] ?? null,
                'payment_method' => $payload['payment_method'] ?? null,
                'comment' => $payload['comment'] ?? null,
                'location_id' => $payload['location_id'] ?? null,
                'type' => $type,
                'original_group_id' => $payload['original_group_id'] ?? null,
            ]);

            $total = 0;

            // 1) Возвраты (увеличиваем остатки, сумма со знаком минус)
            foreach (($payload['items_return'] ?? []) as $ret) {
                $price = (int) $ret['price'];
                $count = (int) $ret['count']; // сколько возвращаем
                $discount = (int) ($ret['discount'] ?? 0);

                $order = Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'product_id' => $ret['product_id'],
                    'size_id' => $ret['size_id'] ?? null,
                    'color_id' => $ret['color_id'] ?? null,
                    'price' => $price,
                    'discount' => $discount,
                    'count' => $count,                 // положительное число
                    'original_order_id' => $ret['original_order_id'] ?? null,
                ]);

                // Возврат: увеличиваем остаток
                if (!empty($ret['size_id'])) {
                    $ps = ProductSize::where('product_id', $ret['product_id'])
                        ->where('size_id', $ret['size_id'])
                        ->lockForUpdate()
                        ->first();
                    if ($ps) {
                        $ps->increment('count', $count);
                    }
                }

                $line = ($discount ?: $price) * $count;
                $total -= $line; // возврат уменьшает сумму
            }

            // 2) Новые продажи (для обмена) или обычная продажа
            foreach (($payload['items_sale'] ?? $payload['items'] ?? []) as $it) {
                $price = (int) $it['price'];
                $count = (int) $it['count'];
                $discount = (int) ($it['discount'] ?? 0);

                Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'product_id' => $it['product_id'],
                    'size_id' => $it['size_id'] ?? null,
                    'color_id' => $it['color_id'] ?? null,
                    'price' => $price,
                    'discount' => $discount,
                    'count' => $count,
                ]);

                // Продажа: уменьшаем остаток
                if (!empty($it['size_id'])) {
                    $ps = ProductSize::where('product_id', $it['product_id'])
                        ->where('size_id', $it['size_id'])
                        ->lockForUpdate()
                        ->first();
                    if ($ps) {
                        $ps->decrement('count', $count);
                    }
                }

                $line = ($discount ?: $price) * $count;
                $total += $line;
            }

            // POS — сразу success
            $group->update([
                'total' => $total,
                'status' => OrderStatusEnum::SUCCESS,
                'paid_at' => now(),
                'order_number' => now()->format('YmdHis') . $group->id,
            ]);

            return $group;
        });
    }
    // public function create(array $payload): OrderGroup
    // {
    //     return DB::transaction(function () use ($payload) {
    //         // создаём группу
    //         $group = OrderGroup::create([
    //             'user_id'        => $payload['user_id']        ?? null,
    //             'status'         => OrderStatusEnum::PENDING,
    //             'source'         => $payload['source'],                 // 'pos' | 'app'
    //             'cashier_id'     => $payload['cashier_id']     ?? null,
    //             'payment_method' => $payload['payment_method'] ?? null,
    //             'comment'        => $payload['comment']        ?? null,
    //             'location_id'    => $payload['location_id']    ?? null, // 👈 не забудь добавить в $fillable
    //         ]);

    //         $total = 0;

    //         foreach ($payload['items'] as $it) {
    //             $price    = (int) $it['price'];
    //             $discount = (int) ($it['discount'] ?? 0);
    //             $count    = (int) $it['count'];

    //             // строка заказа (фиксируем моментальную цену/скидку)
    //             Order::create([
    //                 'order_group_id' => $group->id,
    //                 'user_id'        => $payload['user_id'] ?? null, // см. чек-лист ниже
    //                 'product_id'     => (int) $it['product_id'],
    //                 'size_id'        => $it['size_id']   ?? null,
    //                 'color_id'       => $it['color_id']  ?? null,
    //                 'price'          => $price,
    //                 'discount'       => $discount,
    //                 'count'          => $count,
    //             ]);

    //             // списываем остаток по размеру (если есть)
    //             if (!empty($it['size_id'])) {
    //                 $ps = ProductSize::where('product_id', (int) $it['product_id'])
    //                     ->where('size_id',   (int) $it['size_id'])
    //                     ->lockForUpdate()
    //                     ->first();

    //                 if ($ps) {
    //                     // не даём уйти в минус
    //                     $new = max(0, (int)$ps->count - $count);
    //                     $ps->update(['count' => $new]);
    //                 }
    //             }

    //             // если discount — это "цена со скидкой", используем её; иначе — обычную цену
    //             $line = ($discount ?: $price) * $count;
    //             $total += $line;
    //         }

    //         // POS — сразу успех, APP — остаётся PENDING
    //         $isPos = $payload['source'] === 'pos';

    //         $group->update([
    //             'total'        => $total,
    //             'status'       => $isPos ? OrderStatusEnum::SUCCESS : OrderStatusEnum::PENDING,
    //             'paid_at'      => $isPos ? now() : null,
    //             'order_number' => $isPos
    //                 ? (now()->format('YmdHis') . $group->id)
    //                 : $group->order_number,
    //         ]);

    //         return $group;
    //     });
    // }
}