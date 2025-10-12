<?php

namespace App\Filament\Pages\POS;

use Filament\Notifications\Notification;

trait CustomerTrait
{
    public ?int $customerId = null;
    public ?string $customerName = null;
    public ?string $customerPhone = null;
    public ?int $discountPct = 0;
    public bool $applyDiscount = true;

    public function findCustomerByPhone(): void
    {
        $phone = trim((string) $this->customerPhone);
        if ($phone === '') {
            Notification::make()->title('Введите телефон клиента')->warning()->send();
            return;
        }
        $normalized = preg_replace('/\D+/', '', $phone);

        $user = \App\Models\User::query()
            ->whereRaw("regexp_replace(phone, '\\D', '', 'g') = ?", [$normalized])
            ->orWhere('phone', $phone)
            ->first();

        if ($user) {
            $this->customerId = $user->id;
            $this->customerName = $user->full_name ?: $user->phone;
            Notification::make()->title('Клиент найден')->body("{$this->customerName} (ID: {$this->customerId})")->success()->send();
        } else {
            $this->customerId = null; $this->customerName = null;
            Notification::make()->title('Клиент не найден')->body('Можно создать гостя или провести продажу без клиента.')->warning()->send();
        }
    }

    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerName = null;
        $this->customerPhone = null;
    }

    public function createGuestCustomer(): void
    {
        $phone = trim((string) $this->customerPhone);
        if ($phone === '') {
            Notification::make()->title('Введите номер телефона')->warning()->send();
            return;
        }
        if (\App\Models\User::where('phone', $phone)->exists()) {
            Notification::make()->title('Клиент уже существует')->info()->send();
            return;
        }

        $user = \App\Models\User::create([
            'phone' => $phone,
            'full_name' => 'Гость ' . substr($phone, -4),
            'is_first_order' => false,
            'balance' => 0,
        ]);

        $this->customerId = $user->id;
        $this->customerName = $user->full_name;

        Notification::make()->title('Создан новый клиент')->body("ID: {$user->id}, {$user->phone}")->success()->send();
    }

    protected function inferDiscountFromCustomer(): void
    {
        if (!empty($this->customerId)) {
            $card = \App\Models\User::where('id', $this->customerId)->value('card');
            $map = ['black' => 10, 'gold' => 7, 'silver' => 5];
            if ($card && isset($map[strtolower($card)])) { $this->discountPct = $map[strtolower($card)]; return; }
        }
        $this->discountPct = 0;
    }

    protected function discountedUnitPrice(int $price): int
    {
        $pct = (int) ($this->discountPct ?? 0);
        if ($pct <= 0) return (int)$price;
        return (int) floor($price * (100 - $pct) / 100);
    }

    private function normalizePhone(?string $raw): ?string
    {
        if (!$raw) return null;
        $digits = preg_replace('/\D+/', '', $raw);
        if (strlen($digits) === 9) $digits = '998'.$digits;
        return strlen($digits) >= 9 ? $digits : null;
    }

    private function ensureCustomer(): ?\App\Models\User
    {
        if ($this->customerId) return \App\Models\User::find($this->customerId);

        $phone = $this->normalizePhone($this->customerPhone);
        if (!$phone) return null;

        $user = \App\Models\User::where('phone', $phone)->first();
        if ($user) {
            if ($this->customerName && empty($user->full_name)) {
                $user->full_name = $this->customerName; $user->save();
            }
            $this->customerId = $user->id;
            return $user;
        }

        $user = \App\Models\User::create([
            'phone' => $phone,
            'full_name' => $this->customerName ?: 'POS Client',
            'is_first_order' => true,
        ]);

        $this->customerId = $user->id;
        return $user;
    }
}