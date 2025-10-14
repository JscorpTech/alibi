<?php

namespace App\Services\Sales;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\Variant;
use App\Services\Inventory\StockService;  // ⭐ НОВОЕ
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderWriter
{
    // ⭐ ДОБАВИТЬ КОНСТРУКТОР
    public function __construct(
        private StockService $stockService
    ) {
    }

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
                $this->validateReturnGroup($payload['original_group_id']);
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
                    $this->validateReturnQuantity($ret['original_order_id'], $count, $index);
                }

                // Получаем данные варианта для снимка
                $variant = Variant::with('product')->find($variantId);

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
                    'product_name' => $variant?->product?->name,  // ⭐ НОВОЕ
                    'variant_sku' => $variant?->sku,              // ⭐ НОВОЕ
                ]);

                // ⭐ ЗАМЕНЕНО: вместо increaseVariantStock используем StockService
                $this->stockService->increase(
                    variantId: $variantId,
                    qty: $count,
                    reason: 'return',
                    source: $source,
                    orderGroupId: $group->id
                );

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

                // Получаем данные варианта для снимка
                $variant = Variant::with('product')->find($variantId);

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
                    'product_name' => $variant?->product?->name,  // ⭐ НОВОЕ
                    'variant_sku' => $variant?->sku,              // ⭐ НОВОЕ
                ]);

                // ⭐ ЗАМЕНЕНО: вместо decreaseVariantStock используем StockService
                $this->stockService->deduct(
                    variantId: $variantId,
                    qty: $count,
                    source: $source,
                    orderGroupId: $group->id
                );

                $total += max(0, $price - $discount) * $count;
            }

            $group->update([
                'total' => $total,
                'status' => OrderStatusEnum::SUCCESS,
                'paid_at' => now(),
                'order_number' => now()->format('YmdHis') . $group->id,
            ]);

            if ($type === 'return') {
                Log::info('✅ Возврат завершен через StockService', [
                    'return_group_id' => $group->id,
                    'order_number' => $group->order_number,
                    'total' => $group->total,
                ]);
            }

            return $group;
        });
    }

    // ========================================
    // ⭐ НОВЫЕ МЕТОДЫ (вынесены из create)
    // ========================================

    /**
     * Проверить что не возвращаем по возвратному чеку
     */
    private function validateReturnGroup(int $originalGroupId): void
    {
        $original = OrderGroup::find($originalGroupId);

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
            'original_group_id' => $originalGroupId,
            'original_type' => $original?->type ?? 'unknown',
        ]);
    }

    /**
     * Проверить что не возвращаем больше чем продали
     */
    private function validateReturnQuantity(int $originalOrderId, int $count, int $index): void
    {
        $originalOrder = Order::where('id', $originalOrderId)
            ->lockForUpdate()
            ->first();

        if (!$originalOrder) {
            throw new \RuntimeException("Оригинальный заказ #{$originalOrderId} не найден");
        }

        $soldQty = (int) $originalOrder->count;

        // Сколько уже возвращено по этой позиции
        $returnedQty = (int) Order::where('original_order_id', $originalOrder->id)
            ->sum('count');

        $remaining = max(0, $soldQty - $returnedQty);

        Log::info("📦 Проверка возврата позиции #{$index}", [
            'original_order_id' => $originalOrder->id,
            'product_id' => $originalOrder->product_id,
            'variant_id' => $originalOrder->variant_id,
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
                "По позиции #{$originalOrder->id} (товар ID:{$originalOrder->product_id}) уже всё возвращено! " .
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

    // ========================================
    // ⚠️ DEPRECATED: Старые методы (НЕ ИСПОЛЬЗУЮТСЯ)
    // Оставлены для обратной совместимости
    // ========================================

    /**
     * @deprecated Используй StockService::increase() вместо этого
     */
    private function increaseVariantStock(int $variantId, int $qty): void
    {
        Log::warning('⚠️ Используется deprecated метод increaseVariantStock', [
            'variant_id' => $variantId,
            'qty' => $qty,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2),
        ]);

        Variant::where('id', $variantId)
            ->lockForUpdate()
            ->update(['stock' => DB::raw('COALESCE(stock,0) + ' . (int) $qty)]);
    }

    /**
     * @deprecated Используй StockService::deduct() вместо этого
     */
    private function decreaseVariantStock(int $variantId, int $qty): void
    {
        Log::warning('⚠️ Используется deprecated метод decreaseVariantStock', [
            'variant_id' => $variantId,
            'qty' => $qty,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2),
        ]);

        $affected = Variant::where('id', $variantId)
            ->where('stock', '>=', $qty)
            ->lockForUpdate()
            ->decrement('stock', $qty);

        if ($affected === 0) {
            throw new \RuntimeException("Variant #{$variantId}: insufficient stock");
        }
    }
}