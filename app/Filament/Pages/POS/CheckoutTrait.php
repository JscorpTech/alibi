<?php

namespace App\Filament\Pages\POS;

use Filament\Notifications\Notification;

trait CheckoutTrait
{
    public string $paymentMethod = 'cash';
    public ?int $locationId = null;
    public ?string $comment = null;
    public array $locations = [];
    public array $recent = [];
    public bool $debug = false;

    public function loadRecent(): void
    {
        $this->recent = \App\Models\OrderGroup::query()
            ->with([
                'user:id,full_name,phone',
                'orders' => fn($q) => $q->select('id','order_group_id','product_id','size_id','color_id','price','count'),
                'orders.product:id,name_ru,image',
                'orders.color:id,name',
                'orders.size:id,name',
            ])
            ->where('source','pos')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'number' => $g->order_number ?? $g->id,
                    'total' => (int)($g->total ?? 0),
                    'status' => (string)$g->status,
                    'payment' => $g->payment_method,
                    'created_at' => $g->created_at?->format('d.m H:i'),
                    'items_count' => $g->orders->count(),
                    'customer' => ['name' => $g->user?->full_name, 'phone' => $g->user?->phone],
                    'items' => $g->orders->map(fn($o) => [
                        'order_id' => (int)$o->id,
                        'product_id' => (int)$o->product_id,
                        'size_id' => $o->size_id ? (int)$o->size_id : null,
                        'color_id' => $o->color_id ? (int)$o->color_id : null,
                        'name' => $o->product?->name_ru ?? 'Без названия',
                        'price' => (int)$o->price,
                        'count' => (int)$o->count,
                        'size' => $o->size?->name,
                        'color' => $o->color?->name,
                        'image' => $this->fileUrl($o->product?->image),
                    ])->toArray(),
                ];
            })
            ->toArray();
    }

    public function mount(): void
    {
        $this->debug = (bool) env('POS_DEBUG', false);
        $this->loadRecent();
        $this->locations = \App\Models\StockLocation::orderBy('name')->pluck('name','id')->toArray();
        $this->locationId = $this->locationId ?? \DB::table('stock_locations')->where('code','store_1')->value('id');
    }

    protected function selectFields(): array
    {
        $fields = ['id','price'];
        foreach (['name_ru','name','image'] as $c) if (\Illuminate\Support\Facades\Schema::hasColumn('products',$c)) $fields[] = $c;
        return $fields;
    }

    public function checkout(): void
    {
        if ($this->mode === 'sale' && empty($this->cart)) {
            Notification::make()->title('Корзина пуста')->danger()->send(); return;
        }
        if (in_array($this->mode, ['return','exchange'])) {
            $hasReturn = false;
            foreach ($this->returnLines as $r) if ((int)($r['count'] ?? 0) > 0) { $hasReturn = true; break; }
            if (!$hasReturn) { Notification::make()->title('Не выбраны позиции для возврата')->danger()->send(); return; }
        }

        $customer = $this->ensureCustomer();
        $userId = $customer?->id;

        $itemsSale = array_map(function (array $row) {
            return [
                'product_id' => (int)$row['id'],
                'variant_id' => $row['variant_id'] ?? null,
                'size_id' => isset($row['size_id']) ? (int)$row['size_id'] : null,
                'color_id' => $row['color_id'] ?? null,
                'count' => (int)$row['qty'],
                'price' => (int)$row['price'],
            ];
        }, $this->cart);

        $itemsReturn = [];
        if (in_array($this->mode, ['return','exchange'])) {
            foreach ($this->returnLines as $r) {
                $c   = (int)($r['count'] ?? 0);
                $max = (int)($r['max'] ?? 0);
                if ($c > 0 && $c <= $max) {
                    $itemsReturn[] = [
                        'original_order_id' => $r['original_order_id'] ?? null,
                        'product_id' => (int)$r['product_id'],
                        'variant_id' => $r['variant_id'] ?? null,
                        'size_id' => $r['size_id'] ?? null,
                        'color_id' => $r['color_id'] ?? null,
                        'count' => $c,
                        'price' => (int)$r['price'],
                    ];
                }
            }
        }

        try {
            /** @var \App\Services\Sales\OrderWriter $writer */
            $writer = app(\App\Services\Sales\OrderWriter::class);

            $locationId = $this->locationId ?? \DB::table('stock_locations')->where('code','store_1')->value('id');

            $originalGroupId = null;
            if ($this->originalNumber && in_array($this->mode,['return','exchange'])) {
                $originalGroupId = \App\Models\OrderGroup::where('order_number',$this->originalNumber)
                    ->orWhere('id',(int)$this->originalNumber)->value('id');
            }

            $payload = [
                'type' => $this->mode, // sale | return | exchange
                'user_id' => $userId,
                'source' => 'pos',
                'cashier_id' => auth()->id(),
                'payment_method' => $this->paymentMethod,
                'comment' => $this->comment,
                'location_id' => $locationId,
                'original_group_id' => $originalGroupId,
            ];

            if ($this->mode === 'sale') {
                $payload['items'] = $itemsSale;
            } elseif ($this->mode === 'return') {
                $payload['items_return'] = $itemsReturn;
                $payload['items_sale'] = [];
            } else { // exchange
                $payload['items_return'] = $itemsReturn;
                $payload['items_sale'] = $itemsSale;
            }

            $group = $writer->create($payload);

            $this->clearCart();
            $this->barcode = ''; $this->results = []; $this->returnLines = []; $this->originalNumber = null;

            $label = match ($this->mode) {
                'return' => 'Возврат оформлен',
                'exchange' => 'Обмен оформлен',
                default => 'Продажа проведена',
            };

            Notification::make()->title($label)
                ->body('Чек № '.($group->order_number ?? $group->id).' • Сумма: '.number_format((int)($group->total ?? 0)).' сум')
                ->success()->send();

            $total = (int)($group->total ?? 0);
            $orderNo = $group->order_number ?? $group->id;
            $cashier = auth()->user()->full_name ?? auth()->user()->name ?? ('ID:'.(auth()->id() ?? '—'));
            $caption = match ($this->mode) {'return'=>'ВОЗВРАТ','exchange'=>'ОБМЕН',default=>'POS продажа'};

            $msg = "🧾 <b>{$caption}</b>\nЧек № <b>{$orderNo}</b>\nСумма: <b>".number_format($total,0,'.',' ')."</b> сум\nОплата: <b>".($this->paymentMethod ?? '—')."</b>\nКассир: <b>{$cashier}</b>"
                 . ($customer ? ("\nКлиент: <b>".e($customer->full_name ?: $customer->phone)."</b>") : '');

            app(\App\Services\PosBotService::class)->send(env('POS_TELEGRAM_CHAT_ID'), $msg);

            $this->loadRecent();

        } catch (\Throwable $e) {
            report($e);
            Notification::make()->title('Ошибка при оформлении')->body($e->getMessage())->danger()->send();
        }
    }
}