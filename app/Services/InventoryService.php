<?php
namespace App\Services;

use App\Models\InventoryLevel;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService {
    public function reserve(int $variantId, int $locationId, int $qty, ?int $orderId=null): void {
        DB::transaction(function () use ($variantId,$locationId,$qty,$orderId) {
            $level = InventoryLevel::lockForUpdate()
              ->firstOrCreate(['product_variant_id'=>$variantId,'stock_location_id'=>$locationId]);
            if ($level->qty_on_hand - $level->qty_reserved < $qty) {
                throw new \RuntimeException('Недостаточно остатков');
            }
            $level->qty_reserved += $qty; $level->save();
            StockMovement::create([
                'product_variant_id'=>$variantId,
                'stock_location_id'=>$locationId,
                'delta'=>0,'reason'=>'reservation','order_id'=>$orderId,
            ]);
        });
    }

    public function decreaseOnHand(int $variantId, int $locationId, int $qty, ?int $orderId=null): void {
        DB::transaction(function () use ($variantId,$locationId,$qty,$orderId) {
            $level = InventoryLevel::lockForUpdate()
              ->firstOrCreate(['product_variant_id'=>$variantId,'stock_location_id'=>$locationId]);
            if ($level->qty_on_hand < $qty) throw new \RuntimeException('Нет на складе');
            $deReserve = min($level->qty_reserved, $qty);
            $level->qty_reserved -= $deReserve;
            $level->qty_on_hand  -= $qty;
            $level->save();
            StockMovement::create([
                'product_variant_id'=>$variantId,
                'stock_location_id'=>$locationId,
                'delta'=>-$qty,'reason'=>'sale','order_id'=>$orderId,
            ]);
        });
    }

    public function transfer(int $variantId, int $fromLoc, int $toLoc, int $qty): void {
        DB::transaction(function () use ($variantId,$fromLoc,$toLoc,$qty) {
            $from = InventoryLevel::lockForUpdate()
              ->firstOrCreate(['product_variant_id'=>$variantId,'stock_location_id'=>$fromLoc]);
            if ($from->qty_on_hand < $qty) throw new \RuntimeException('Нет на складе (from)');
            $to = InventoryLevel::lockForUpdate()
              ->firstOrCreate(['product_variant_id'=>$variantId,'stock_location_id'=>$toLoc]);

            $from->qty_on_hand -= $qty; $from->save();
            $to->qty_on_hand   += $qty; $to->save();

            StockMovement::create(['product_variant_id'=>$variantId,'stock_location_id'=>$fromLoc,'delta'=>-$qty,'reason'=>'transfer']);
            StockMovement::create(['product_variant_id'=>$variantId,'stock_location_id'=>$toLoc,  'delta'=>+$qty,'reason'=>'transfer']);
        });
    }
}