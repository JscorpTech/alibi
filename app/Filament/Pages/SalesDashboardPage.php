<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Services\Reports\SalesReport;

class SalesDashboardPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Products'; // 👈 теперь под меню Products
    protected static ?string $navigationLabel = 'Sales report';
    protected static ?string $title = 'Аналитика продаж';
    protected static ?int $navigationSort = 20;
    protected static string $view = 'filament.pages.sales-dashboard';

    // Параметры фильтрации
    public ?string $from = null;
    public ?string $to = null;
    public ?int $locationId = null;

    // Активная вкладка
    public string $tab = 'allsales';

    // Данные
    public array $kpi = [];
    public array $top = [];
    public array $byCashier = [];
    public array $sizeColor = [];
    public array $stock = [];
    public array $allSales = [];
    public array $locations = [];

    // новые свойства
    public bool $showGroupModal = false;
    public array $groupCard = [];   // шапка чека
    public array $groupItems = [];  // позиции чека



    public function openGroup(int $groupId): void
    {
        $g = \App\Models\OrderGroup::with([
            'orders.product:id,name_ru,image',
            'orders.size:id,name',
            'orders.color:id,name',
            // берём у пользователей full_name и email вместо name
            'cashier:id,full_name,email',
            'user:id,full_name,email',
        ])->find($groupId);

        if (!$g) {
            return;
        }

        $this->groupCard = [
            'id' => $g->id,
            'order_number' => $g->order_number,
            'paid_at' => optional($g->paid_at)->format('Y-m-d H:i'),
            'type' => $g->type,
            'source' => $g->source,
            'total' => (int) $g->total,
            // показываем ФИО, если пусто — email, если и его нет — "—"
            'cashier' => $g->cashier->full_name ?? $g->cashier->email ?? '—',
            'client' => $g->user->full_name ?? $g->user->email ?? '—',
        ];

        $this->groupItems = $g->orders->map(function ($o) {
            $price = (int) $o->price;
            $discount = (int) ($o->discount ?? 0);
            $qty = (int) $o->count;
            $line = max(0, $price - $discount) * $qty;

            return [
                'product_id' => (int) $o->product_id,
                'name' => $o->product->name_ru ?? ('Товар #' . $o->product_id),
                'image' => $o->product->image ?? null, // в Blade можно прогнать через Storage::url()
                'size' => $o->size->name ?? null,
                'color' => $o->color->name ?? null,
                'price' => $price,
                'discount' => $discount,
                'qty' => $qty,
                'line_total' => $line,
            ];
        })->toArray();

        $this->showGroupModal = true;
    }

    public function closeGroup(): void
    {
        $this->showGroupModal = false;
        $this->groupCard = [];
        $this->groupItems = [];
    }

    public function mount(): void
    {
        $today = now()->toDateString(); // 'YYYY-MM-DD'
        $this->from = $today;
        $this->to = $today;


        if (\Schema::hasTable('stock_locations')) {
            $this->locations = \App\Models\StockLocation::orderBy('name')->pluck('name', 'id')->toArray();
        }

        $this->refreshReport();
    }

    public function updated($field): void
    {
        if (in_array($field, ['from', 'to', 'locationId'])) {
            $this->refreshReport();
        }
    }


    public function setToday(): void
    {
        $today = now()->toDateString();
        $this->from = $today;
        $this->to = $today;
        $this->refreshReport();
    }

    public function setTab(string $tab): void
    {
        $allowed = ['allsales', 'kpi', 'top', 'cashiers', 'sizecolor', 'stock'];
        $this->tab = in_array($tab, $allowed, true) ? $tab : 'allsales';
    }

    public function refreshReport(): void
    {
        $rep = app(\App\Services\Reports\SalesReport::class);

        // нормализуем границы
        $fromDate = (string) $this->from;
        $toDate = (string) $this->to;

        // если пользователь случайно поменял местами
        if (strtotime($fromDate) > strtotime($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
            $this->from = $fromDate;
            $this->to = $toDate;
        }

        $start = $fromDate . ' 00:00:00';
        $end = $toDate . ' 23:59:59';

        $loc = $this->locationId ?? null;

        $this->kpi = $rep->kpis($start, $end, $loc);
        $this->top = $rep->topProducts($start, $end, 20, $loc);
        $this->byCashier = $rep->byCashier($start, $end, $loc);
        $this->sizeColor = $rep->bySizeColor($start, $end, $loc);
        $this->stock = $rep->stockSnapshot($loc);
        $this->allSales = $rep->allSales($start, $end, $loc);
    }
    /**
     * Передаём данные в Blade-шаблон
     */
    protected function getViewData(): array
    {
        return [
            'tab' => $this->tab,
            'tabs' => [
                'allsales' => 'Все продажи',
                'top' => 'Топ товары',
                'cashiers' => 'Кассиры',
                'sizecolor' => 'Размер/Цвет',
                'stock' => 'Остатки',
                'kpi' => 'KPI',
            ],
            'kpi' => $this->kpi,
            'top' => $this->top,
            'byCashier' => $this->byCashier,
            'sizeColor' => $this->sizeColor,
            'stock' => $this->stock,
            'allSales' => $this->allSales,
            'locations' => $this->locations,
        ];
    }
}