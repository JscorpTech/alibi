<?php

namespace App\Services\Sales;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\ProductSize;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ReturnService
{
    /**
     * Создание чека-возврата по исходному заказу.
     *
     * @param array{
     *   original_group_id:int,
     *   user_id?:int|null,
     *   cashier_id?:int|null,
     *   payment_method?:string|null,
     *   comment?:string|null,
     *   lines: array<int, array{
     *      order_id:int,
     *      product_id:int,
     *      price:int,
     *      count:int,
     *      variant_id?:int|null,
     *      size_id?:int|null,
     *      color_id?:int|null,
     *   }>,
     * } $payload
     */
    // public function create(array $payload): OrderGroup
    // {
    //     return DB::transaction(function () use ($payload) {

    //         // 0) Исходный чек + fallback для user_id
    //         $original = OrderGroup::findOrFail((int) $payload['original_group_id']);

    //         // ✅ ЗАЩИТА #1: Нельзя делать возврат по возвратному чеку!
    //         if ($original->type === 'return') {
    //             Log::warning('Попытка возврата по возвратному чеку', [
    //                 'original_group_id' => $original->id,
    //                 'original_type' => $original->type,
    //             ]);
    //             throw new \RuntimeException("Нельзя делать возврат по возвратному чеку! Используйте оригинальный чек продажи #" . ($original->original_group_id ?? $original->id));
    //         }

    //         // ✅ LOGGING: Начало возврата
    //         Log::info('=== НАЧАЛО ВОЗВРАТА ===', [
    //             'original_group_id' => $original->id,
    //             'original_type' => $original->type,
    //             'lines_count' => count($payload['lines']),
    //         ]);

    //         $userId = $payload['user_id']
    //             ?? $original->user_id
    //             ?? (int) env('POS_GUEST_USER_ID', 1);

    //         // Какие механизмы склада доступны
    //         $hasVariantsStock = Schema::hasTable('variants') && Schema::hasColumn('variants', 'stock');
    //         $hasProductSizes = Schema::hasTable('product_sizes')
    //             && Schema::hasColumn('product_sizes', 'count')
    //             && Schema::hasColumn('product_sizes', 'product_id')
    //             && Schema::hasColumn('product_sizes', 'size_id');

    //         // 1) Шапка чека-возврата
    //         $group = OrderGroup::create([
    //             'user_id' => $userId,
    //             'status' => OrderStatusEnum::SUCCESS,
    //             'source' => 'pos',
    //             'type' => 'return',
    //             'cashier_id' => $payload['cashier_id'] ?? null,
    //             'payment_method' => $payload['payment_method'] ?? 'refund',
    //             'comment' => trim(($payload['comment'] ?? '') . ' (возврат по чеку #' . $original->id . ')'),
    //             'paid_at' => now(),
    //             'original_group_id' => $original->id,
    //         ]);

    //         Log::info('Создана группа возврата', [
    //             'return_group_id' => $group->id,
    //         ]);

    //         $total = 0;

    //         foreach ($payload['lines'] as $lineIndex => $line) {
    //             $count = (int) ($line['count'] ?? 0);
    //             if ($count <= 0) {
    //                 continue;
    //             }

    //             // 2) Находим исходную позицию и лочим её
    //             /** @var Order $src */
    //             $src = Order::where('id', (int) $line['order_id'])
    //                 ->where('order_group_id', $original->id)
    //                 ->lockForUpdate()
    //                 ->firstOrFail();

    //             $soldQty = (int) $src->count;

    //             // ✅ КЛЮЧЕВАЯ ПРОВЕРКА: Сколько уже возвращено
    //             $returnedQty = (int) Order::where('original_order_id', $src->id)->sum('count');

    //             $remaining = max(0, $soldQty - $returnedQty);

    //             // ✅ ДЕТАЛЬНЫЙ LOGGING
    //             Log::info("Проверка позиции #{$lineIndex}", [
    //                 'order_id' => $src->id,
    //                 'product_id' => $line['product_id'],
    //                 'продано_изначально' => $soldQty,
    //                 'уже_возвращено_ранее' => $returnedQty,
    //                 'осталось_можно_вернуть' => $remaining,
    //                 'пытается_вернуть_сейчас' => $count,
    //             ]);

    //             // ✅ ЗАЩИТА #2: Проверка лимитов
    //             if ($remaining <= 0) {
    //                 Log::error('Все товары уже возвращены', [
    //                     'order_id' => $src->id,
    //                     'sold' => $soldQty,
    //                     'returned' => $returnedQty,
    //                 ]);
    //                 throw new \RuntimeException("По позиции #{$src->id} (товар ID:{$line['product_id']}) уже всё возвращено! Продано было: {$soldQty} шт, возвращено: {$returnedQty} шт.");
    //             }

    //             if ($count > $remaining) {
    //                 Log::error('Попытка вернуть больше чем осталось', [
    //                     'order_id' => $src->id,
    //                     'remaining' => $remaining,
    //                     'trying_to_return' => $count,
    //                 ]);
    //                 throw new \RuntimeException("Нельзя вернуть {$count} шт по позиции #{$src->id}. Осталось доступно для возврата: {$remaining} шт (продано: {$soldQty}, уже возвращено: {$returnedQty}).");
    //             }

    //             // 3) Создаём строку возврата
    //             $price = (int) $line['price'];
    //             $variantId = isset($line['variant_id']) ? (int) $line['variant_id'] : ($src->variant_id ?: null);
    //             $sizeId = isset($line['size_id']) ? (int) $line['size_id'] : ($src->size_id ?: null);

    //             $returnOrder = Order::create([
    //                 'order_group_id' => $group->id,
    //                 'user_id' => $userId,
    //                 'product_id' => (int) $line['product_id'],
    //                 'variant_id' => $variantId,
    //                 'size_id' => $sizeId,
    //                 'color_id' => $line['color_id'] ?? $src->color_id,
    //                 'price' => $price,
    //                 'discount' => 0,
    //                 'count' => $count,
    //                 'original_order_id' => $src->id,
    //             ]);

    //             Log::info('Создана строка возврата', [
    //                 'return_order_id' => $returnOrder->id,
    //                 'original_order_id' => $src->id,
    //                 'count' => $count,
    //             ]);

    //             // 4) Возвращаем на склад
    //             $this->increaseStock(
    //                 variantId: $variantId,
    //                 productId: (int) $line['product_id'],
    //                 sizeId: $sizeId,
    //                 qty: $count,
    //                 canUseVariantsStock: $hasVariantsStock,
    //                 canUseProductSizes: $hasProductSizes
    //             );

    //             // 5) Сумма возврата
    //             $lineSum = $price * $count;
    //             $total += $lineSum;
    //         }

    //         // 6) Итог: отрицательная сумма + номер чека
    //         $group->update([
    //             'total' => -abs($total),
    //             'order_number' => now()->format('YmdHis') . $group->id,
    //         ]);

    //         Log::info('=== ВОЗВРАТ ЗАВЕРШЕН ===', [
    //             'return_group_id' => $group->id,
    //             'order_number' => $group->order_number,
    //             'total' => $group->total,
    //         ]);

    //         return $group;
    //     });
    // }

    // /* =============================== Stock Helpers =============================== */

    // protected function increaseStock(
    //     ?int $variantId,
    //     int $productId,
    //     ?int $sizeId,
    //     int $qty,
    //     bool $canUseVariantsStock,
    //     bool $canUseProductSizes
    // ): void {
    //     $done = false;

    //     if ($canUseVariantsStock && $variantId) {
    //         Variant::where('id', $variantId)
    //             ->lockForUpdate()
    //             ->update([
    //                 'stock' => DB::raw('GREATEST(0, COALESCE(stock,0) + ' . (int) $qty . ')'),
    //             ]);
    //         $done = true;
    //         Log::info('Возврат на склад (variants.stock)', [
    //             'variant_id' => $variantId,
    //             'qty' => $qty,
    //         ]);
    //     }

    //     if (!$done && $canUseProductSizes && $sizeId) {
    //         $ps = ProductSize::where('product_id', $productId)
    //             ->where('size_id', $sizeId)
    //             ->lockForUpdate()
    //             ->first();

    //         if ($ps) {
    //             $ps->increment('count', (int) $qty);
    //             Log::info('Возврат на склад (product_sizes)', [
    //                 'product_id' => $productId,
    //                 'size_id' => $sizeId,
    //                 'qty' => $qty,
    //             ]);
    //         }
    //     }
    // }
}