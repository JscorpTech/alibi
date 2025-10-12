<?php

namespace App\Services\Sales;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\ProductSize;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderWriter
{
    /**
     * Создаёт OrderGroup + Orders, корректирует остатки и считает total.
     *
     * Ожидаемый payload:
     * [
     *   'type'              => 'sale'|'return'|'exchange',      // default: sale
     *   'source'            => 'pos'|'app',                     // default: pos
     *   'cashier_id'        => int|null,
     *   'payment_method'    => string|null,
     *   'comment'           => string|null,
     *   'location_id'       => int|null,
     *   'original_group_id' => int|null,
     *   'user_id'           => int|null,                        // клиент (user)
     *
     *   // для sale:      items ИЛИ items_sale
     *   // для return:    items_return
     *   // для exchange:  items_return + items_sale
     *   'items' | 'items_sale' => [
     *      [
     *          'product_id' => int,
     *          'variant_id' => int|null,
     *          'size_id'    => int|null,
     *          'color_id'   => int|null,
     *          'count'      => int,
     *          'price'      => int,       // цена за 1 шт
     *          'discount'   => int|null,  // скидка за 1 шт (0 если нет)
     *      ],
     *      ...
     *   ],
     *   'items_return' => [...] // те же поля; count — сколько возвращается
     * ]
     */
    public function create(array $payload): OrderGroup
    {
        return DB::transaction(function () use ($payload) {
            $type = $payload['type'] ?? 'sale';
            $source = $payload['source'] ?? 'pos';

            // Проверка возможностей по управлению остатками
            $hasVariantsStock = Schema::hasTable('variants') && Schema::hasColumn('variants', 'stock');
            $hasProductSizes = Schema::hasTable('product_sizes')
                && Schema::hasColumn('product_sizes', 'count')
                && Schema::hasColumn('product_sizes', 'product_id')
                && Schema::hasColumn('product_sizes', 'size_id');

            // Создаём группу
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

            foreach (($payload['items_return'] ?? []) as $ret) {
                $productId = (int) $ret['product_id'];
                $variantId = isset($ret['variant_id']) ? (int) $ret['variant_id'] : null;
                $sizeId = isset($ret['size_id']) ? (int) $ret['size_id'] : null;
                $price = (int) $ret['price'];
                $discount = (int) ($ret['discount'] ?? 0);
                $count = (int) $ret['count'];

                // Строка возврата
                Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'size_id' => $sizeId,
                    'color_id' => $ret['color_id'] ?? null,
                    'price' => $price,
                    'discount' => $discount,
                    'count' => $count,
                    'original_order_id' => $ret['original_order_id'] ?? null,
                ]);

          
                $this->increaseStock(
                    variantId: $variantId,
                    productId: $productId,
                    sizeId: $sizeId,
                    qty: $count,
                    canUseVariantsStock: $hasVariantsStock,
                    canUseProductSizes: $hasProductSizes
                );

                // Уменьшаем total
                $line = max(0, $price - $discount) * $count;
                $total -= $line;
            }

            $saleItems = $payload['items_sale'] ?? ($payload['items'] ?? []);
            foreach ($saleItems as $it) {
                $productId = (int) $it['product_id'];
                $variantId = isset($it['variant_id']) ? (int) $it['variant_id'] : null;
                $sizeId = isset($it['size_id']) ? (int) $it['size_id'] : null;
                $price = (int) $it['price'];
                $discount = (int) ($it['discount'] ?? 0);
                $count = (int) $it['count'];

                // Строка продажи
                Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'size_id' => $sizeId,
                    'color_id' => $it['color_id'] ?? null,
                    'price' => $price,
                    'discount' => $discount,
                    'count' => $count,
                ]);

                // -count из остатков
                $this->decreaseStock(
                    variantId: $variantId,
                    productId: $productId,
                    sizeId: $sizeId,
                    qty: $count,
                    canUseVariantsStock: $hasVariantsStock,
                    canUseProductSizes: $hasProductSizes
                );

                // Увеличиваем total
                $line = max(0, $price - $discount) * $count;
                $total += $line;
            }

            // Финализация группы
            $group->update([
                'total' => $total,
                'status' => OrderStatusEnum::SUCCESS,
                'paid_at' => now(),
                'order_number' => now()->format('YmdHis') . $group->id,
            ]);

            return $group;
        });
    }

    /* =============================== Stock Helpers =============================== */

    protected function increaseStock(
        ?int $variantId,
        int $productId,
        ?int $sizeId,
        int $qty,
        bool $canUseVariantsStock,
        bool $canUseProductSizes
    ): void {
        $done = false;

        if ($canUseVariantsStock && $variantId) {
            Variant::where('id', $variantId)
                ->lockForUpdate()
                ->update([
                    'stock' => DB::raw('GREATEST(0, COALESCE(stock,0) + ' . (int) $qty . ')'),
                ]);
            $done = true;
        }

        if (!$done && $canUseProductSizes && $sizeId) {
            $ps = ProductSize::where('product_id', $productId)
                ->where('size_id', $sizeId)
                ->lockForUpdate()
                ->first();

            $ps?->increment('count', (int) $qty);
        }
    }

    protected function decreaseStock(
        ?int $variantId,
        int $productId,
        ?int $sizeId,
        int $qty,
        bool $canUseVariantsStock,
        bool $canUseProductSizes
    ): void {
        $done = false;

        if ($canUseVariantsStock && $variantId) {
            Variant::where('id', $variantId)
                ->lockForUpdate()
                ->update([
                    'stock' => DB::raw('GREATEST(0, COALESCE(stock,0) - ' . (int) $qty . ')'),
                ]);
            $done = true;
        }

        if (!$done && $canUseProductSizes && $sizeId) {
            $ps = ProductSize::where('product_id', $productId)
                ->where('size_id', $sizeId)
                ->lockForUpdate()
                ->first();

            $ps?->decrement('count', (int) $qty);
        }
    }
}