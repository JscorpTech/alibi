<?php

namespace App\Filament\Pages\POS;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait HelpersTrait
{
    public function dbg(string $msg, array $ctx = []): void
    {
        if (!($this->debug ?? false)) return;
        Log::info('[POS] '.$msg, array_merge([
            'mode' => $this->mode ?? null,
            'barcode' => $this->barcode ?? null,
            'customer_id' => $this->customerId ?? null,
        ], $ctx));
    }

    protected function imageUrl(object $p): ?string
    {
        if (!Schema::hasColumn('products','image') || empty($p->image)) return null;
        if (!is_string($p->image)) return null;

        return str_starts_with($p->image,'http')
            ? $p->image
            : asset('storage/'.ltrim($p->image,'/'));
    }

    protected function fileUrl(?string $path): ?string
    {
        if (empty($path)) return null;
        if (str_starts_with($path,'http')) return $path;
        $path = preg_replace('#^/?public/#','',$path);
        return asset('storage/'.ltrim($path,'/'));
    }
}