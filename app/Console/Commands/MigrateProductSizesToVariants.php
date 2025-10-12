<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Variant;

class MigrateProductSizesToVariants extends Command
{
    protected $signature = 'migrate:variants {--chunk=500} {--dry-run}';
    protected $description = 'Migrate product_sizes -> variants (convert existing pivots into variants)';

    public function handle()
    {
        $chunk = (int) $this->option('chunk');
        $dry = (bool) $this->option('dry-run');

        $this->info("Start migration product_sizes -> variants (chunk={$chunk})" . ($dry ? ' [DRY RUN]' : ''));

        DB::table('product_sizes')
            ->orderBy('product_id')
            ->chunkById($chunk, function ($rows) use ($dry) {
                foreach ($rows as $r) {
                    // size name
                    $sizeName = DB::table('sizes')->where('id', $r->size_id)->value('name') ?? null;
                    // product (для цены/sku)
                    $product = DB::table('products')->where('id', $r->product_id)->first();

                    if (!$product) {
                        $this->warn("Skip: product not found id={$r->product_id}");
                        continue;
                    }

                    $sku = $r->sku ?? ($product->sku ?? null);
                    $attrs = ['Size' => (string) $sizeName]; // нормализованные ключи
                    $data = [
                        'price' => (int) ($product->price ?? 0),
                        'sku' => $sku,
                        'stock' => (int) ($r->count ?? 0),
                        'available' => true,
                        // image не трогаем в миграции
                    ];

                    if ($dry) {
                        $this->line("[DRY] upsert Variant by (product_id+attrs): product_id={$r->product_id} sku={$sku} stock={$data['stock']} attrs=" . json_encode($attrs));
                        continue;
                    }

                    try {
                        // ищем существующую запись по product_id + JSON-равенству attrs (PostgreSQL jsonb)
                        $existing = Variant::query()
                            ->where('product_id', $r->product_id)
                            ->whereRaw('attrs::jsonb = ?::jsonb', [json_encode($attrs)])
                            ->first();

                        if ($existing) {
                            // обновляем без трогания barcode (он уже есть/останется)
                            $existing->fill($data)->save();
                        } else {
                            // создаём через Eloquent БЕЗ barcode → сработает Variant::creating и модель сама его присвоит
                            Variant::create(array_merge($data, [
                                'product_id' => $r->product_id,
                                'attrs' => $attrs,
                            ]));
                        }
                    } catch (\Throwable $e) {
                        $this->error("Failed upsert variant for product_id={$r->product_id}: " . $e->getMessage());
                    }
                }
            });

        $this->info('Migration finished.');
    }
}