<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
                    $sizeName = DB::table('sizes')->where('id', $r->size_id)->value('name') ?? null;
                    $product = DB::table('products')->where('id', $r->product_id)->first();
                    if (!$product) {
                        $this->warn("Skip: product not found id={$r->product_id}");
                        continue;
                    }

                    $sku = $r->sku ?? ($product->sku ?? null);
                    $barcode = $r->barcode ?? ($product->barcode ?? null);

                    if (empty($barcode)) {
                        // Простая генерация уникального 13-значного числового кода
                        $barcode = $this->generateUniqueBarcode();
                        $this->info("Generated barcode {$barcode} for product_id={$r->product_id}, size_id={$r->size_id}");
                    }

                    $insert = [
                        'product_id' => $r->product_id,
                        'price' => $product->price ?? 0,
                        'sku' => $sku,
                        'barcode' => $barcode,
                        'stock' => (int) ($r->count ?? 0),
                        'attrs' => json_encode(['Size' => $sizeName]),
                        'image' => null,
                        'available' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if ($dry) {
                        $this->line("[DRY] Would insert variant: product_id={$r->product_id} sku={$sku} barcode={$barcode} stock={$insert['stock']}");
                    } else {
                        try {
                            DB::table('variants')->insert($insert);
                        } catch (\Throwable $e) {
                            $this->error("Failed insert variant for product_id={$r->product_id}: " . $e->getMessage());
                        }
                    }
                }
            });

        $this->info('Migration finished.');
    }

    protected function generateUniqueBarcode(): string
    {
        // Очень простая генерация 13 цифр; можно заменить на корректный EAN13 generator
        do {
            $code = str_pad((string) random_int(0, 9999999999999), 13, '0', STR_PAD_LEFT);
            $exists = DB::table('variants')->where('barcode', $code)->exists()
                || DB::table('products')->where('barcode', $code)->exists()
                || DB::table('product_sizes')->where('barcode', $code)->exists();
        } while ($exists);

        return $code;
    }
}