<?php

namespace App\Services\Inventory;

use App\Models\InventoryLevel;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Находим/создаём уровень остатка под блокировкой.
     */
    protected function ensureLevelForUpdate(int $productId, ?int $sizeId, int $locationId): InventoryLevel
    {
        // Сначала пробуем найти запись под блокировкой:
        $level = InventoryLevel::where('product_id', $productId)
            ->when($sizeId, fn($q) => $q->where('size_id', $sizeId))
            ->where('stock_location_id', $locationId)
            ->lockForUpdate()
            ->first();

        if ($level) {
            return $level;
        }

        // Если нет — создаём (в той же транзакции, без гонок).
        $level           = new InventoryLevel();
        $level->product_id        = $productId;
        $level->size_id           = $sizeId;          // может быть null
        $level->stock_location_id = $locationId;
        $level->qty_on_hand       = 0;
        $level->qty_reserved      = 0;
        $level->save();

        // и заново берём под lock (не обязательно, но можно):
        return InventoryLevel::whereKey($level->id)->lockForUpdate()->first();
    }

    /**
     * Приёмка на склад (+qty).
     */
    public function receive(int $productId, ?int $sizeId, int $locationId, int $qty, array $meta = []): void
    {
        if ($qty <= 0) throw new \InvalidArgumentException('qty must be > 0');

        DB::transaction(function () use ($productId, $sizeId, $locationId, $qty, $meta) {
            $level = $this->ensureLevelForUpdate($productId, $sizeId, $locationId);
            $level->qty_on_hand += $qty;
            $level->save();

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $locationId,
                'delta'             => +$qty,
                'reason'            => 'receive',
                'order_id'          => $meta['order_id'] ?? null,
                'meta'              => $meta,
            ]);
        });
    }

    /**
     * Резерв (0-движение, только увеличиваем qty_reserved).
     */
    public function reserve(int $productId, ?int $sizeId, int $locationId, int $qty, array $meta = []): void
    {
        if ($qty <= 0) throw new \InvalidArgumentException('qty must be > 0');

        DB::transaction(function () use ($productId, $sizeId, $locationId, $qty, $meta) {
            $level = $this->ensureLevelForUpdate($productId, $sizeId, $locationId);

            $available = $level->qty_on_hand - $level->qty_reserved;
            if ($available < $qty) {
                throw new \RuntimeException('Недостаточно доступного остатка для резерва');
            }

            $level->qty_reserved += $qty;
            $level->save();

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $locationId,
                'delta'             => 0,
                'reason'            => 'reservation',
                'order_id'          => $meta['order_id'] ?? null,
                'meta'              => $meta,
            ]);
        });
    }

    /**
     * Снять резерв (частично или полностью).
     */
    public function release(int $productId, ?int $sizeId, int $locationId, int $qty, array $meta = []): void
    {
        if ($qty <= 0) throw new \InvalidArgumentException('qty must be > 0');

        DB::transaction(function () use ($productId, $sizeId, $locationId, $qty, $meta) {
            $level = $this->ensureLevelForUpdate($productId, $sizeId, $locationId);
            $release = min($level->qty_reserved, $qty);
            if ($release <= 0) return;

            $level->qty_reserved -= $release;
            $level->save();

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $locationId,
                'delta'             => 0,
                'reason'            => 'reservation_release',
                'order_id'          => $meta['order_id'] ?? null,
                'meta'              => $meta + ['released' => $release],
            ]);
        });
    }

    /**
     * Продажа (списываем on_hand, уменьшаем резерв если был). Поддерживает идемпотентность по meta['key'].
     */
    public function sell(int $productId, ?int $sizeId, int $locationId, int $qty, array $meta = []): void
    {
        if ($qty <= 0) throw new \InvalidArgumentException('qty must be > 0');

        DB::transaction(function () use ($productId, $sizeId, $locationId, $qty, $meta) {
            // Идемпотентность: если пришёл уникальный ключ (напр. receipt_id) — не дублируем
            if (!empty($meta['key'])) {
                $exists = StockMovement::where([
                        'product_id'        => $productId,
                        'size_id'           => $sizeId,
                        'stock_location_id' => $locationId,
                        'reason'            => 'sale',
                    ])
                    ->where('meta->key', $meta['key'])
                    ->exists();

                if ($exists) {
                    return; // уже списали по этому ключу
                }
            }

            $level = $this->ensureLevelForUpdate($productId, $sizeId, $locationId);

            if ($level->qty_on_hand < $qty) {
                throw new \RuntimeException('Нет достаточного остатка на складе');
            }

            // сначала снимаем резерв (если был)
            $fromReserved = min($level->qty_reserved, $qty);
            if ($fromReserved > 0) {
                $level->qty_reserved -= $fromReserved;
            }

            // затем списываем со склада
            $level->qty_on_hand -= $qty;
            $level->save();

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $locationId,
                'delta'             => -$qty,
                'reason'            => 'sale',
                'order_id'          => $meta['order_id'] ?? null,
                'meta'              => $meta + ['from_reserved' => $fromReserved],
            ]);
        });
    }

    /**
     * Перемещение между складами (две записи: -qty и +qty).
     */
    public function transfer(int $productId, ?int $sizeId, int $fromLoc, int $toLoc, int $qty, array $meta = []): void
    {
        if ($qty <= 0) throw new \InvalidArgumentException('qty must be > 0');
        if ($fromLoc === $toLoc) return;

        DB::transaction(function () use ($productId, $sizeId, $fromLoc, $toLoc, $qty, $meta) {
            $from = $this->ensureLevelForUpdate($productId, $sizeId, $fromLoc);
            if ($from->qty_on_hand < $qty) {
                throw new \RuntimeException('Нет достаточного остатка на исходном складе');
            }

            $to = $this->ensureLevelForUpdate($productId, $sizeId, $toLoc);

            $from->qty_on_hand -= $qty;
            $from->save();

            $to->qty_on_hand += $qty;
            $to->save();

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $fromLoc,
                'delta'             => -$qty,
                'reason'            => 'transfer',
                'meta'              => $meta + ['direction' => 'out', 'to' => $toLoc],
            ]);

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $toLoc,
                'delta'             => +$qty,
                'reason'            => 'transfer',
                'meta'              => $meta + ['direction' => 'in', 'from' => $fromLoc],
            ]);
        });
    }

    /**
     * Корректировка (инвентаризация): выставляем qty_on_hand = факту.
     */
    public function adjustTo(int $productId, ?int $sizeId, int $locationId, int $newQty, array $meta = []): void
    {
        if ($newQty < 0) throw new \InvalidArgumentException('newQty must be >= 0');

        DB::transaction(function () use ($productId, $sizeId, $locationId, $newQty, $meta) {
            $level = $this->ensureLevelForUpdate($productId, $sizeId, $locationId);

            $delta = $newQty - $level->qty_on_hand;
            if ($delta === 0) return;

            $level->qty_on_hand = $newQty;
            $level->save();

            StockMovement::create([
                'product_id'        => $productId,
                'size_id'           => $sizeId,
                'stock_location_id' => $locationId,
                'delta'             => $delta, // может быть + или -
                'reason'            => 'adjustment',
                'meta'              => $meta + ['new_qty' => $newQty],
            ]);
        });
    }
}