<?php

namespace App\Filament\Pages\POS;

use App\Models\OrderGroup;
use Filament\Notifications\Notification;

trait ReceiptTrait
{
    public $selectedReceipt = null;

    /**
     * Zagruzit' dannie cheka dlya pechati
     */
    public function loadReceiptData($orderId)
    {
        try {
            // Zagruzhaem chek so vsemi tovarami
            $order = OrderGroup::with([
                'orders.product',
                'orders.size',
                'orders.color',
                'orders.variant',
                'user'
            ])->findOrFail($orderId);
            
            // Sohranyaem v svoystvo
            $this->selectedReceipt = [
                'id' => $order->id,
                'number' => $order->order_number,
                'created_at' => $order->paid_at?->format('d.m.Y H:i') ?? $order->created_at->format('d.m.Y H:i'),
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'customer' => $order->user?->name ?? 'Гость',
                'customer_phone' => $order->user?->phone ?? '',
                'cashier' => auth()->user()->name,
                'source' => $order->source ?? 'pos',
                'items' => $order->orders->map(function($item) {
                    return [
                        'name' => $item->product->name_ru ?? $item->product->name ?? 'Товар',
                        'count' => $item->count,
                        'price' => $item->price,
                        'discount' => $item->discount ?? 0,
                        'size' => $item->size?->name,
                        'color' => $item->color?->name,
                        'sku' => $item->variant?->sku ?? '',
                        'total' => ($item->price - ($item->discount ?? 0)) * $item->count,
                    ];
                })->toArray(),
            ];
            
            // Signal dlya JavaScript
            $this->dispatch('receipt-loaded');
            
            \Log::info('Receipt loaded successfully', ['order_id' => $orderId]);
            
        } catch (\Exception $e) {
            \Log::error('Receipt load error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            Notification::make()
                ->title('Ошибка загрузки чека')
                ->body('Не удалось загрузить данные чека для печати')
                ->danger()
                ->send();
        }
    }
}