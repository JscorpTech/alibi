<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\OrderGroup;

class PosOrders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'POS чеки';
    protected static ?string $slug = 'pos-orders';
    protected static string $view = 'filament.pages.pos-orders';

    public array $orders = [];

    public function mount(): void
    {
        $this->loadOrders();
    }

    protected function loadOrders(): void
    {
        $this->orders = OrderGroup::query()
            ->with(['orders.product:id,name_ru,image', 'orders.size:id,name', 'orders.color:id,name'])
            ->where('source', 'pos')
            ->latest('id')
            ->limit(30)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'number' => $g->order_number ?? ('#' . $g->id),
                    'total' => number_format($g->total ?? 0, 0, '.', ' '),
                    'created_at' => $g->created_at->format('d.m H:i'),
                    'payment_method' => $g->payment_method ?? '-',
                    'items' => $g->orders->map(fn($o) => [
                        'name' => $o->product?->name_ru ?? '—',
                        'size' => $o->size?->name ?? null,
                        'color' => $o->color?->name ?? null,
                        'qty' => $o->count,
                        'price' => number_format($o->price, 0, '.', ' '),
                    ]),
                ];
            })
            ->toArray();
    }
}