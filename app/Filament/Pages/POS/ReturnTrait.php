<?php

namespace App\Filament\Pages\POS;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ReturnTrait
{
    public string $mode = 'sale'; // sale|return|exchange
    public ?string $originalNumber = null;
    public ?int $originalGroupId = null;
    public array $returnLines = []; // [{order_id, product_id, size_id, color_id, price, count}]
    public bool $showRecentItemModal = false;
    public array $recentItem = [];

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['sale','return','exchange']) ? $mode : 'sale';
        if ($this->mode === 'sale') {
            $this->originalNumber = null;
            $this->originalGroupId = null;
            $this->returnLines = [];
        }
    }

    public function getCanSubmitReturnProperty(): bool
    {
        if ($this->mode !== 'return') return false;
        if (!$this->originalGroupId) return false;
        if (empty($this->returnLines)) return false;

        foreach ($this->returnLines as $line) {
            $count = (int)($line['count'] ?? 0);
            $max   = (int)($line['max'] ?? 0);
            if ($count < 0 || $count > $max) return false;
        }
        $sum = array_sum(array_map(fn($l) => (int)($l['count'] ?? 0), $this->returnLines));
        return $sum > 0;
    }

    public function getHasReturnSelectionProperty(): bool
    {
        foreach ($this->returnLines as $r) if ((int)($r['count'] ?? 0) > 0) return true;
        return false;
    }

    public function getCanStartExchangeProperty(): bool
    {
        return $this->mode === 'exchange' && !empty($this->originalGroupId) && $this->hasReturnSelection;
    }

    public function loadOriginal(): void
    {
        $number = trim((string)$this->originalNumber);
        if ($number === '') { $this->returnLines = []; $this->originalGroupId = null; return; }

        $g = \App\Models\OrderGroup::with(['orders.product:id,name_ru','orders.size:id,name','orders.color:id,name'])
            ->where(fn($q) => $q->where('order_number',$number)->orWhere('id',(int)$number))
            ->first();

        if (!$g) {
            $this->returnLines = []; $this->originalGroupId = null;
            Notification::make()->title('Чек не найден')->danger()->send();
            return;
        }
        $this->originalGroupId = $g->id;

        $this->returnLines = $g->orders->map(function ($o) {
            return [
                'order_id' => (int)$o->id,
                'product_id' => (int)$o->product_id,
                'name' => $o->product?->name_ru ?? ('Товар #'.$o->product_id),
                'price' => (int)$o->price,
                'max' => (int)$o->count,
                'count' => 0,
                'size_id' => $o->size_id,
                'size_name' => $o->size?->name,
                'color_id' => $o->color_id,
                'color_name' => $o->color?->name,
            ];
        })->toArray();

        Notification::make()->title('Чек загружен')->success()->send();
    }

    public function openRecentItem(int $groupId, int $productId, ?int $sizeId = null): void
    {
        $q = DB::table('orders as o')
            ->join('products as p','p.id','=','o.product_id')
            ->leftJoin('sizes as s','s.id','=','o.size_id')
            ->leftJoin('colors as c','c.id','=','o.color_id')
            ->leftJoin('product_sizes as ps', function ($j) {
                $j->on('ps.product_id','=','o.product_id')->on('ps.size_id','=','o.size_id');
            })
            ->where('o.order_group_id',$groupId)
            ->where('o.product_id',$productId);

        if ($sizeId !== null) $q->where('o.size_id',$sizeId);

        $select = [
            'o.id as order_id','o.price','o.count','o.size_id','o.color_id',
            'p.id as product_id','p.image','s.name as size_name','c.name as color_name',
            'ps.barcode','ps.sku',
        ];
        if (Schema::hasColumn('products','name_ru')) $select[] = 'p.name_ru';
        if (Schema::hasColumn('products','name'))    $select[] = 'p.name';

        $row = $q->select($select)->first();
        if (!$row) { Notification::make()->title('Позиция не найдена')->danger()->send(); return; }

        $productName = $row->name_ru ?? ($row->name ?? ('Товар #'.$row->product_id));

        $this->recentItem = [
            'order_id' => (int)$row->order_id,
            'product_id' => (int)$row->product_id,
            'name' => $productName,
            'price' => (int)$row->price,
            'count' => (int)$row->count,
            'size_id' => $row->size_id ? (int)$row->size_id : null,
            'size_name' => $row->size_name,
            'color_id' => $row->color_id ? (int)$row->color_id : null,
            'color_name' => $row->color_name,
            'barcode' => $row->barcode,
            'sku' => $row->sku,
            'image' => $this->fileUrl($row->image ?? null),
        ];
        $this->showRecentItemModal = true;
    }

    public function closeRecentItemModal(): void
    {
        $this->showRecentItemModal = false;
        $this->recentItem = [];
    }

    public function startExchange(): void
    {
        \Filament\Notifications\Notification::make()
            ->title('Обмен: выберите новые товары для клиента')
            ->success()->send();
    }
}