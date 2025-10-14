<?php

namespace App\Services\Inventory;

use App\Models\Variant;
use App\Models\StockMovement;
use App\Models\OrderGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Зарезервировать товар (для APP корзины)
     */
    public function reserve(int $variantId, int $qty, int $orderGroupId): void
    {
        DB::transaction(function () use ($variantId, $qty, $orderGroupId) {
            $variant = Variant::where('id', $variantId)
                ->lockForUpdate()
                ->firstOrFail();

            $available = $variant->stock - ($variant->reserved_stock ?? 0);

            if ($available < $qty) {
                throw new \RuntimeException(
                    "Variant #{$variantId}: недостаточно товара. " .
                    "Доступно: {$available}, требуется: {$qty}"
                );
            }

            $stockBefore = $variant->stock;
            $reservedBefore = $variant->reserved_stock ?? 0;

            $variant->increment('reserved_stock', $qty);
            $variant->refresh();

            $this->log(
                variantId: $variantId,
                type: 'reserve_app',
                quantity: $qty,
                quantityBefore: $stockBefore,
                quantityAfter: $variant->stock,
                orderGroupId: $orderGroupId,
                note: "Резервирование: reserved {$reservedBefore} → " . ($reservedBefore + $qty)
            );

            Log::info('📦 Товар зарезервирован', [
                'variant_id' => $variantId,
                'qty' => $qty,
                'stock' => $variant->stock,
                'reserved_before' => $reservedBefore,
                'reserved_after' => $variant->reserved_stock,
                'available' => $variant->stock - $variant->reserved_stock,
                'order_group_id' => $orderGroupId,
            ]);
        });
    }

    /**
     * Списать товар со склада
     */
    public function deduct(int $variantId, int $qty, string $source, int $orderGroupId): void
    {
        DB::transaction(function () use ($variantId, $qty, $source, $orderGroupId) {
            $variant = Variant::where('id', $variantId)
                ->lockForUpdate()
                ->firstOrFail();

            $stockBefore = $variant->stock;
            $reservedBefore = $variant->reserved_stock ?? 0;

            // Для APP: сначала снимаем резерв (если есть), потом stock
            if ($source === 'app' && $reservedBefore > 0) {
                $toRelease = min($qty, $reservedBefore);
                $variant->decrement('reserved_stock', $toRelease);
            }

            // Проверка доступности
            if ($variant->stock < $qty) {
                throw new \RuntimeException(
                    "Variant #{$variantId}: недостаточно на складе. " .
                    "Есть: {$variant->stock}, требуется: {$qty}"
                );
            }

            // Списываем stock
            $variant->decrement('stock', $qty);
            $variant->refresh();

            $type = $source === 'pos' ? 'sale_pos' : 'sale_app';

            $this->log(
                variantId: $variantId,
                type: $type,
                quantity: -$qty,
                quantityBefore: $stockBefore,
                quantityAfter: $variant->stock,
                orderGroupId: $orderGroupId,
                note: $source === 'app' ? "reserved: {$reservedBefore} → {$variant->reserved_stock}" : null
            );

            Log::info('📉 Списание со склада', [
                'variant_id' => $variantId,
                'qty' => $qty,
                'source' => $source,
                'stock_before' => $stockBefore,
                'stock_after' => $variant->stock,
                'reserved_before' => $reservedBefore,
                'reserved_after' => $variant->reserved_stock,
                'order_group_id' => $orderGroupId,
            ]);
        });
    }

    /**
     * Вернуть товар на склад (возврат или отмена)
     */
    public function increase(
        int $variantId,
        int $qty,
        string $reason,
        string $source,
        int $orderGroupId
    ): void {
        DB::transaction(function () use ($variantId, $qty, $reason, $source, $orderGroupId) {
            $variant = Variant::where('id', $variantId)
                ->lockForUpdate()
                ->firstOrFail();

            $stockBefore = $variant->stock;

            // Увеличиваем stock
            $variant->increment('stock', $qty);
            $variant->refresh();

            $type = match ($reason) {
                'return' => $source === 'pos' ? 'return_pos' : 'return_app',
                'cancel' => $source === 'pos' ? 'cancel_pos' : 'cancel_app',
                default => 'adjustment',
            };

            $this->log(
                variantId: $variantId,
                type: $type,
                quantity: $qty,
                quantityBefore: $stockBefore,
                quantityAfter: $variant->stock,
                orderGroupId: $orderGroupId,
                note: "Reason: {$reason}"
            );

            Log::info('📈 Возврат на склад', [
                'variant_id' => $variantId,
                'qty' => $qty,
                'reason' => $reason,
                'source' => $source,
                'stock_before' => $stockBefore,
                'stock_after' => $variant->stock,
                'order_group_id' => $orderGroupId,
            ]);
        });
    }

    /**
     * Снять резерв (если заказ не оплатили)
     */
    public function releaseReserve(int $variantId, int $qty, int $orderGroupId): void
    {
        DB::transaction(function () use ($variantId, $qty, $orderGroupId) {
            $variant = Variant::where('id', $variantId)
                ->lockForUpdate()
                ->firstOrFail();

            $reservedBefore = $variant->reserved_stock ?? 0;
            $toRelease = min($qty, $reservedBefore);

            if ($toRelease > 0) {
                $variant->decrement('reserved_stock', $toRelease);
                $variant->refresh();

                $this->log(
                    variantId: $variantId,
                    type: 'release_reserve',
                    quantity: -$toRelease,
                    quantityBefore: $variant->stock,
                    quantityAfter: $variant->stock,
                    orderGroupId: $orderGroupId,
                    note: "Снятие резерва: {$reservedBefore} → {$variant->reserved_stock}"
                );

                Log::info('🔓 Резерв снят', [
                    'variant_id' => $variantId,
                    'qty' => $toRelease,
                    'reserved_before' => $reservedBefore,
                    'reserved_after' => $variant->reserved_stock,
                    'order_group_id' => $orderGroupId,
                ]);
            }
        });
    }

    /**
     * Отменить заказ (вернуть всё на склад)
     */
    public function cancelOrder(int $orderGroupId): void
    {
        DB::transaction(function () use ($orderGroupId) {
            $orderGroup = OrderGroup::with('orders')
                ->lockForUpdate()
                ->findOrFail($orderGroupId);

            if (!$orderGroup->canBeCanceled()) {
                throw new \RuntimeException(
                    "Нельзя отменить заказ в статусе: {$orderGroup->status}"
                );
            }

            // Возвращаем товары
            foreach ($orderGroup->orders as $order) {
                // Если был резерв (pending) - снимаем
                if ($orderGroup->status === 'pending' && $orderGroup->source === 'app') {
                    $this->releaseReserve($order->variant_id, $order->count, $orderGroupId);
                } else {
                    // Если уже списан - возвращаем на склад
                    $this->increase(
                        variantId: $order->variant_id,
                        qty: $order->count,
                        reason: 'cancel',
                        source: $orderGroup->source,
                        orderGroupId: $orderGroupId
                    );
                }
            }

            Log::info('❌ Заказ отменен через StockService', [
                'order_group_id' => $orderGroupId,
                'order_number' => $orderGroup->order_number,
                'source' => $orderGroup->source,
                'items_count' => $orderGroup->orders->count(),
            ]);
        });
    }

    /**
     * Получить текущий остаток
     */
    public function getStock(int $variantId): int
    {
        return Variant::where('id', $variantId)->value('stock') ?? 0;
    }

    /**
     * Получить доступный остаток (stock - reserved)
     */
    public function getAvailableStock(int $variantId): int
    {
        $variant = Variant::find($variantId);
        return $variant ? $variant->available_stock : 0;
    }

    /**
     * Проверить доступность
     */
    public function isAvailable(int $variantId, int $qty): bool
    {
        return $this->getAvailableStock($variantId) >= $qty;
    }

    /**
     * Логировать движение склада
     */
    private function log(
        int $variantId,
        string $type,
        int $quantity,
        int $quantityBefore,
        int $quantityAfter,
        ?int $orderGroupId = null,
        ?string $note = null
    ): void {
        try {
            StockMovement::create([
                'variant_id' => $variantId,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'order_group_id' => $orderGroupId,
                'user_id' => auth()->id(),
                'note' => $note,

                // Для обратной совместимости со старым форматом
                'delta' => $quantity,
                'reason' => $this->mapTypeToReason($type),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log stock movement', [
                'variant_id' => $variantId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Маппинг type → reason (для обратной совместимости)
     */
    private function mapTypeToReason(string $type): string
    {
        return match ($type) {
            'sale_pos', 'sale_app' => 'sale',
            'return_pos', 'return_app' => 'return',
            'cancel_pos', 'cancel_app' => 'adjustment',
            'reserve_app', 'release_reserve' => 'adjustment',
            'purchase' => 'receive',
            default => 'adjustment',
        };
    }
}