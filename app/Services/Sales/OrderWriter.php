<?php

namespace App\Services\Sales;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderWriter
{
    public function create(array $payload): OrderGroup
    {
        return DB::transaction(function () use ($payload) {
            $type = $payload['type'] ?? 'sale';
            $source = $payload['source'] ?? 'pos';

            // ✅ работаем ТОЛЬКО с variants.stock
            if (!Schema::hasTable('variants') || !Schema::hasColumn('variants', 'stock')) {
                throw new \RuntimeException('variants.stock is required');
            }

            // ✅ ЗАЩИТА: Нельзя делать возврат по возвратному чеку!
            if ($type === 'return' && !empty($payload['original_group_id'])) {
                $original = OrderGroup::find((int) $payload['original_group_id']);

                if ($original && $original->type === 'return') {
                    Log::warning('❌ Попытка возврата по возвратному чеку', [
                        'original_group_id' => $original->id,
                        'original_type' => $original->type,
                        'original_original_group_id' => $original->original_group_id,
                    ]);

                    throw new \RuntimeException(
                        "Нельзя делать возврат по возвратному чеку! " .
                        "Используйте оригинальный чек продажи #" .
                        ($original->original_group_id ?? $original->id)
                    );
                }

                Log::info('✅ Начало возврата', [
                    'original_group_id' => $payload['original_group_id'],
                    'original_type' => $original->type ?? 'unknown',
                ]);
            }

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

            // ---- RETURN LINES (stock++) ----
            foreach (($payload['items_return'] ?? []) as $index => $ret) {
                $productId = (int) $ret['product_id'];
                $variantId = (int) ($ret['variant_id'] ?? 0);
                $price = (int) $ret['price'];
                $discount = (int) ($ret['discount'] ?? 0);
                $count = (int) $ret['count'];

                // 🔒 Требуем variant_id
                if ($variantId <= 0) {
                    throw new \InvalidArgumentException('Variant ID is required for return line');
                }

                // ✅ ПРОВЕРКА: Нельзя вернуть больше, чем было продано
                if (!empty($ret['original_order_id'])) {
                    $originalOrder = Order::where('id', (int) $ret['original_order_id'])
                        ->lockForUpdate()
                        ->first();

                    if ($originalOrder) {
                        $soldQty = (int) $originalOrder->count;

                        // Сколько уже возвращено по этой позиции
                        $returnedQty = (int) Order::where('original_order_id', $originalOrder->id)
                            ->sum('count');

                        $remaining = max(0, $soldQty - $returnedQty);

                        Log::info("📦 Проверка возврата позиции #{$index}", [
                            'original_order_id' => $originalOrder->id,
                            'product_id' => $productId,
                            'variant_id' => $variantId,
                            'продано_изначально' => $soldQty,
                            'уже_возвращено' => $returnedQty,
                            'осталось_можно_вернуть' => $remaining,
                            'пытается_вернуть_сейчас' => $count,
                        ]);

                        if ($remaining <= 0) {
                            Log::error('❌ Все товары уже возвращены', [
                                'original_order_id' => $originalOrder->id,
                                'sold' => $soldQty,
                                'returned' => $returnedQty,
                            ]);

                            throw new \RuntimeException(
                                "По позиции #{$originalOrder->id} (товар ID:{$productId}) уже всё возвращено! " .
                                "Продано было: {$soldQty} шт, возвращено: {$returnedQty} шт."
                            );
                        }

                        if ($count > $remaining) {
                            Log::error('❌ Попытка вернуть больше чем осталось', [
                                'original_order_id' => $originalOrder->id,
                                'remaining' => $remaining,
                                'trying_to_return' => $count,
                            ]);

                            throw new \RuntimeException(
                                "Нельзя вернуть {$count} шт по позиции #{$originalOrder->id}. " .
                                "Осталось доступно для возврата: {$remaining} шт " .
                                "(продано: {$soldQty}, уже возвращено: {$returnedQty})."
                            );
                        }
                    }
                }

                Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'size_id' => $ret['size_id'] ?? null,
                    'color_id' => $ret['color_id'] ?? null,
                    'price' => $price,
                    'discount' => $discount,
                    'count' => $count,
                    'original_order_id' => $ret['original_order_id'] ?? null,
                ]);

                $this->increaseVariantStock($variantId, $count);

                $total -= max(0, $price - $discount) * $count;
            }

            // ---- SALE LINES (stock--) ----
            $saleItems = $payload['items_sale'] ?? ($payload['items'] ?? []);
            foreach ($saleItems as $it) {
                $productId = (int) $it['product_id'];
                $variantId = (int) ($it['variant_id'] ?? 0);
                $price = (int) $it['price'];
                $discount = (int) ($it['discount'] ?? 0);
                $count = (int) $it['count'];

                if ($variantId <= 0) {
                    throw new \InvalidArgumentException('Variant ID is required for sale line');
                }

                Order::create([
                    'order_group_id' => $group->id,
                    'user_id' => $payload['user_id'] ?? null,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'size_id' => $it['size_id'] ?? null,
                    'color_id' => $it['color_id'] ?? null,
                    'price' => $price,
                    'discount' => $discount,
                    'count' => $count,
                ]);

                $this->decreaseVariantStock($variantId, $count);

                $total += max(0, $price - $discount) * $count;
            }

            $group->update([
                'total' => $total,
                'status' => OrderStatusEnum::SUCCESS,
                'paid_at' => now(),
                'order_number' => now()->format('YmdHis') . $group->id,
            ]);

            if ($type === 'return') {
                Log::info('✅ Возврат завершен', [
                    'return_group_id' => $group->id,
                    'order_number' => $group->order_number,
                    'total' => $group->total,
                ]);
            }

            return $group;
        });
    }

    // --- ONLY variants.stock from here ---

    private function increaseVariantStock(int $variantId, int $qty): void
    {
        Variant::where('id', $variantId)
            ->lockForUpdate()
            ->update(['stock' => DB::raw('COALESCE(stock,0) + ' . (int) $qty)]);

        Log::info('📈 Возврат на склад', [
            'variant_id' => $variantId,
            'qty' => $qty,
        ]);
    }

    private function decreaseVariantStock(int $variantId, int $qty): void
    {
        // безопасное списание: только если хватает
        $affected = Variant::where('id', $variantId)
            ->where('stock', '>=', $qty)
            ->lockForUpdate()
            ->decrement('stock', $qty);

        if ($affected === 0) {
            throw new \RuntimeException("Variant #{$variantId}: insufficient stock");
        }
    }
}