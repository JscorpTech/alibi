<?php

namespace App\Filament\Pages\POS;

use Filament\Notifications\Notification;

trait CartTrait
{
    /** Корзина (Detail Transaction) */
    public array $cart = [];

    public function inc(int $i): void
    {
        $this->cart[$i]['qty']++;
    }

    public function dec(int $i): void
    {
        if ($this->cart[$i]['qty'] > 1) $this->cart[$i]['qty']--;
        else $this->remove($i);
    }

    public function remove(int $i): void
    {
        unset($this->cart[$i]);
        $this->cart = array_values($this->cart);
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function subtotal(): int
    {
        return array_reduce($this->cart, fn($s, $it) => $s + $it['qty'] * $it['price'], 0);
    }

    public ?int $selectedDiscount = null; // для discount()

    public function discount(): int
    {
        $percent = (int) ($this->selectedDiscount ?? 0);
        if ($percent <= 0) return 0;

        $sum = 0;
        foreach ($this->cart as $it) {
            $line = (int) $it['price'] * (int) $it['qty'];
            $sum += (int) round($line * $percent / 100);
        }
        return $sum;
    }

    public function tax(): int
    {
        return 0;
    }

    public function total(): int
    {
        return max(0, $this->subtotal() - $this->discount() + $this->tax());
    }

    protected function sortCart(): void
    {
        usort($this->cart, function ($a, $b) {
            $n = strcmp(mb_strtolower($a['name']), mb_strtolower($b['name']));
            if ($n !== 0) return $n;
            return strcmp((string) ($a['size_name'] ?? ''), (string) ($b['size_name'] ?? ''));
        });
        $this->cart = array_values($this->cart);
    }

    protected function productToCartItem(
        object $p,
        ?int $sizeId = null,
        ?string $sizeName = null,
        ?string $variantSku = null,
        ?int $colorId = null,
        ?string $colorName = null,
        ?string $imageOverride = null
    ): array {
        $img = is_string($imageOverride) && $imageOverride !== ''
            ? $imageOverride
            : $this->imageUrl($p);

        return [
            'id' => (int) $p->id,
            'name' => $p->name_ru ?? $p->name ?? ('Товар #' . $p->id),
            'price' => (int) ($p->price ?? 0),
            'qty' => 1,
            'image' => $img ?? '',
            'size_id' => $sizeId,
            'size_name' => $sizeName,
            'sku' => $variantSku ?? $p->sku ?? null,
            'color_id' => $colorId,
            'color_name' => $colorName,
        ];
    }
}