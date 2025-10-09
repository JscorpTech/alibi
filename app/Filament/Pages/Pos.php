<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\OrderGroup;
use Illuminate\Support\Facades\DB;
use App\Services\Sales\OrderWriter;

class Pos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Продажи';
    protected static ?string $navigationLabel = 'Касса (POS)';
    protected static string $view = 'filament.pages.pos';

    public string $barcode = '';
    public ?int $chooseProductId = null; // если хочешь подсветить выбранный
    /** Корзина (Detail Transaction) */
    public array $cart = [];
    public array $recent = [];

    /** Результаты поиска по ИМЕНИ (для левой колонки) */
    public array $results = [];

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('feature.pos', true);
    }
    public ?int $customerId = null;       // выбранный user_id (если нашли/создали)
    public ?string $customerName = null;  // для отображения
    public ?string $customerPhone = null; // вводимый телефон
    public string $paymentMethod = 'cash';
    public ?int $locationId = null;
    public ?string $comment = null;
    public array $locations = [];

    public array $selectedColor = []; // [$productId => colorId]
    public array $selectedSize = []; // [$productId => sizeId]

    // в классе Pos
    public bool $showRecentItemModal = false;
    public array $recentItem = [];
    public string $mode = 'sale'; // sale|return|exchange
    public ?string $originalNumber = null; // № исходного чека для возврата/обмена
    public ?int $originalGroupId = null;
    public array $returnLines = []; // [{order_id, product_id, size_id, color_id, price, count}]


    // app/Filament/Pages/Pos.php (фрагмент класса)
    public function getCanSubmitReturnProperty(): bool
    {
        if ($this->mode !== 'return') {
            return false;
        }
        if (!$this->originalGroupId) {
            return false;
        }
        if (empty($this->returnLines)) {
            return false;
        }

        foreach ($this->returnLines as $line) {
            $count = (int) ($line['count'] ?? 0);
            $max = (int) ($line['max'] ?? 0);
            if ($count < 0 || $count > $max) {
                return false;
            }
        }
        // есть хотя бы одна позиция > 0?
        $sum = array_sum(array_map(fn($l) => (int) ($l['count'] ?? 0), $this->returnLines));
        return $sum > 0;
    }
    public function getHasReturnSelectionProperty(): bool
    {
        foreach ($this->returnLines as $r) {
            if ((int) ($r['count'] ?? 0) > 0)
                return true;
        }
        return false;
    }

    public function getCanStartExchangeProperty(): bool
    {
        return $this->mode === 'exchange' && !empty($this->originalGroupId) && $this->hasReturnSelection;
    }


    public function submitReturn(): void
    {
        if (!$this->getCanSubmitReturnProperty()) {
            \Filament\Notifications\Notification::make()->title('Заполните возврат корректно')->danger()->send();
            return;
        }

        $lines = [];
        foreach ($this->returnLines as $l) {
            $cnt = (int) ($l['count'] ?? 0);
            $max = (int) ($l['max'] ?? 0);
            if ($cnt > 0 && $cnt <= $max) {
                $lines[] = [
                    'order_id' => (int) $l['order_id'],
                    'product_id' => (int) $l['product_id'],
                    'price' => (int) $l['price'],
                    'count' => $cnt,
                    'size_id' => $l['size_id'] ?? null,
                    'color_id' => $l['color_id'] ?? null,
                ];
            }
        }

        if (empty($lines)) {
            \Filament\Notifications\Notification::make()->title('Нечего возвращать')->danger()->send();
            return;
        }

        try {
            /** @var \App\Services\Sales\ReturnService $returns */
            $returns = app(\App\Services\Sales\ReturnService::class);

            $customer = $this->ensureCustomer(); // можно привязать клиента по телефону
            $group = $returns->create([
                'original_group_id' => (int) $this->originalGroupId,
                'user_id' => $customer?->id,
                'cashier_id' => auth()->id(),
                'payment_method' => 'refund',
                'comment' => $this->comment,
                'lines' => $lines,
            ]);

            \Filament\Notifications\Notification::make()
                ->title('Возврат оформлен')
                ->body('Чек-возврат № ' . ($group->order_number ?? $group->id) . ' на сумму ' . number_format((int) ($group->total ?? 0), 0, '.', ' ') . ' сум')
                ->success()
                ->send();

            // Телеграм (по желанию)
            app(\App\Services\PosBotService::class)->send(
                env('POS_TELEGRAM_CHAT_ID'),
                "↩️ <b>Оформлен возврат</b>\nЧек № <b>" . ($group->order_number ?? $group->id) . "</b>\nСумма: <b>" . number_format((int) ($group->total ?? 0), 0, '.', ' ') . "</b> сум"
            );

            // сброс формы возврата
            $this->originalNumber = null;
            $this->originalGroupId = null;
            $this->returnLines = [];

            // обновим последние продажи
            $this->loadRecent();

        } catch (\Throwable $e) {
            report($e);
            \Filament\Notifications\Notification::make()
                ->title('Ошибка возврата')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function startExchange(): void
    {
        // можно просто показать уведомление, что теперь добавляй новые товары в чек,
        // а дальше при checkout вызовем ExchangeService.
        \Filament\Notifications\Notification::make()
            ->title('Обмен: выберите новые товары для клиента')
            ->success()->send();
    }
    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['sale', 'return', 'exchange']) ? $mode : 'sale';

        if ($this->mode === 'sale') {
            $this->originalNumber = null;
            $this->originalGroupId = null;
            $this->returnLines = [];
        }
    }

    public function loadOriginal(): void
    {
        $number = trim((string) $this->originalNumber);
        if ($number === '') {
            $this->returnLines = [];
            $this->originalGroupId = null;
            return;
        }

        $g = \App\Models\OrderGroup::with(['orders.product:id,name_ru', 'orders.size:id,name', 'orders.color:id,name'])
            ->where(function ($q) use ($number) {
                $q->where('order_number', $number)->orWhere('id', (int) $number);
            })
            ->first();

        if (!$g) {
            $this->returnLines = [];
            $this->originalGroupId = null;
            \Filament\Notifications\Notification::make()->title('Чек не найден')->danger()->send();
            return;
        }

        $this->originalGroupId = $g->id;

        $this->returnLines = $g->orders->map(function ($o) {
            return [
                'order_id' => (int) $o->id,
                'product_id' => (int) $o->product_id,
                'name' => $o->product?->name_ru ?? ('Товар #' . $o->product_id),
                'price' => (int) $o->price,
                'max' => (int) $o->count,     // максимум можно вернуть как в исходном чеке
                'count' => 0,                   // изначально 0 — кассир вводит вручную
                'size_id' => $o->size_id,
                'size_name' => $o->size?->name,
                'color_id' => $o->color_id,
                'color_name' => $o->color?->name,
            ];
        })->toArray();

        \Filament\Notifications\Notification::make()->title('Чек загружен')->success()->send();
    }
    /** Открыть модал по позиции из БЛОКА "Последние продажи" */
    public function openRecentItem(int $groupId, int $productId, ?int $sizeId = null): void
    {
        $q = DB::table('orders as o')
            ->join('products as p', 'p.id', '=', 'o.product_id')
            ->leftJoin('sizes as s', 's.id', '=', 'o.size_id')
            ->leftJoin('colors as c', 'c.id', '=', 'o.color_id')
            ->leftJoin('product_sizes as ps', function ($j) {
                $j->on('ps.product_id', '=', 'o.product_id')
                    ->on('ps.size_id', '=', 'o.size_id');
            })
            ->where('o.order_group_id', $groupId)
            ->where('o.product_id', $productId);

        if ($sizeId !== null) {
            $q->where('o.size_id', $sizeId);
        }

        // Базовые поля, которые точно есть
        $select = [
            'o.id as order_id',
            'o.price',
            'o.count',
            'o.size_id',
            'o.color_id',
            'p.id as product_id',
            'p.image',
            's.name as size_name',
            'c.name as color_name',
            'ps.barcode',
            'ps.sku',
        ];

        // Название продукта: добавляем ТОЛЬКО существующие колонки
        if (Schema::hasColumn('products', 'name_ru')) {
            $select[] = 'p.name_ru';
        }
        if (Schema::hasColumn('products', 'name')) {
            $select[] = 'p.name';
        }

        $row = $q->select($select)->first();

        if (!$row) {
            \Filament\Notifications\Notification::make()
                ->title('Позиция не найдена')
                ->danger()
                ->send();
            return;
        }

        // Собираем безопасное имя
        $productName = $row->name_ru ?? ($row->name ?? ('Товар #' . $row->product_id));

        $this->recentItem = [
            'order_id' => (int) $row->order_id,
            'product_id' => (int) $row->product_id,
            'name' => $productName,
            'price' => (int) $row->price,
            'count' => (int) $row->count,
            'size_id' => $row->size_id ? (int) $row->size_id : null,
            'size_name' => $row->size_name,
            'color_id' => $row->color_id ? (int) $row->color_id : null,
            'color_name' => $row->color_name,
            'barcode' => $row->barcode,
            'sku' => $row->sku,
            'image' => $this->fileUrl($row->image ?? null),
        ];

        $this->showRecentItemModal = true; // если используешь модалку
    }

    public function closeRecentItemModal(): void
    {
        $this->showRecentItemModal = false;
        $this->recentItem = [];
    }



    public function selectSize(int $productId, int $sizeId): void
    {
        $this->selectedSize[$productId] = $sizeId;
    }

    // app/Filament/Pages/Pos.php

    private function fileUrl(?string $path): ?string
    {
        if (empty($path))
            return null;

        // если уже https://... — отдаём как есть
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // иногда сохраняют как 'public/...' — убираем префикс
        $path = preg_replace('#^/?public/#', '', $path);

        return asset('storage/' . ltrim($path, '/'));
    }

    public function loadRecent(): void
    {
        $this->recent = \App\Models\OrderGroup::query()
            ->with([
                'user:id,full_name,phone',
                'orders' => function ($q) {
                    $q->select('id', 'order_group_id', 'product_id', 'size_id', 'color_id', 'price', 'count');
                },
                'orders.product:id,name_ru,image', // только существующие колонки
                'orders.color:id,name',
                'orders.size:id,name',
            ])
            ->where('source', 'pos')
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'number' => $g->order_number ?? $g->id,
                    'total' => (int) ($g->total ?? 0),
                    'status' => (string) $g->status,
                    'payment' => $g->payment_method,
                    'created_at' => $g->created_at?->format('d.m H:i'),
                    'items_count' => $g->orders->count(),
                    'customer' => [
                        'name' => $g->user?->full_name,
                        'phone' => $g->user?->phone,
                    ],
                    'items' => $g->orders->map(function ($o) {
                        return [
                            'order_id' => (int) $o->id,
                            'product_id' => (int) $o->product_id,
                            'size_id' => $o->size_id ? (int) $o->size_id : null,
                            'color_id' => $o->color_id ? (int) $o->color_id : null,

                            'name' => $o->product?->name_ru ?? 'Без названия',
                            'price' => (int) $o->price,
                            'count' => (int) $o->count,
                            'size' => $o->size?->name,
                            'color' => $o->color?->name,
                            'image' => $this->fileUrl($o->product?->image),
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();
    }

    // Добавление выбранной комбинации в корзину
    public function addSelected(int $productId): void
    {
        // узнаём, есть ли у товара варианты
        $sizes = $this->getSizes($productId);     // уже есть метод
        $colors = $this->getColors($productId);    // добавим ниже небольшой helper

        $needSize = !empty($sizes);
        $needColor = !empty($colors);

        $sizeId = $this->selectedSize[$productId] ?? null;
        $colorId = $this->selectedColor[$productId] ?? null;

        if ($needSize && !$sizeId) {
            \Filament\Notifications\Notification::make()
                ->title('Выберите размер')->warning()->send();
            return;
        }
        if ($needColor && !$colorId) {
            \Filament\Notifications\Notification::make()
                ->title('Выберите цвет')->warning()->send();
            return;
        }

        $this->addToCart($productId, $sizeId, $colorId);
    }

    public function findCustomerByPhone(): void
    {
        $phone = trim((string) $this->customerPhone);
        if ($phone === '') {
            \Filament\Notifications\Notification::make()
                ->title('Введите телефон клиента')
                ->warning()
                ->send();
            return;
        }

        // нормализуем (убираем пробелы, +, скобки и т.п.)
        $normalized = preg_replace('/\D+/', '', $phone);

        $user = \App\Models\User::query()
            ->whereRaw("regexp_replace(phone, '\\D', '', 'g') = ?", [$normalized])
            ->orWhere('phone', $phone)
            ->first();

        if ($user) {
            $this->customerId = $user->id;
            $this->customerName = $user->full_name ?: $user->phone;

            \Filament\Notifications\Notification::make()
                ->title('Клиент найден')
                ->body("{$this->customerName} (ID: {$this->customerId})")
                ->success()
                ->send();
        } else {
            $this->customerId = null;
            $this->customerName = null;

            \Filament\Notifications\Notification::make()
                ->title('Клиент не найден')
                ->body('Можно создать гостя или провести продажу без клиента.')
                ->warning()
                ->send();
        }
    }

    /**
     * Сброс выбранного клиента
     */
    public function clearCustomer(): void
    {
        $this->customerId = null;
        $this->customerName = null;
        $this->customerPhone = null;
    }

    /**
     * Создать гостевого клиента (минимальные данные)
     */
    public function createGuestCustomer(): void
    {
        $phone = trim((string) $this->customerPhone);
        if ($phone === '') {
            \Filament\Notifications\Notification::make()
                ->title('Введите номер телефона')
                ->warning()
                ->send();
            return;
        }

        $exists = \App\Models\User::where('phone', $phone)->exists();
        if ($exists) {
            \Filament\Notifications\Notification::make()
                ->title('Клиент уже существует')
                ->info()
                ->send();
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

        \Filament\Notifications\Notification::make()
            ->title('Создан новый клиент')
            ->body("ID: {$user->id}, {$user->phone}")
            ->success()
            ->send();
    }

    public function mount(): void
    {
        $this->loadRecent();
        $this->locations = \App\Models\StockLocation::orderBy('name')->pluck('name', 'id')->toArray();
        $this->locationId = $this->locationId
            ?? \DB::table('stock_locations')->where('code', 'store_1')->value('id');
    }

    protected function getSizes(int $productId): array
    {
        // product_sizes(size_id, product_id, count), sizes(id, name)
        if (!\Schema::hasTable('product_sizes') || !\Schema::hasTable('sizes'))
            return [];

        return DB::table('product_sizes')
            ->join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_sizes.product_id', $productId)
            ->select('sizes.id', 'sizes.name', 'product_sizes.count')
            ->orderBy('sizes.name')
            ->get()
            ->map(fn($r) => [
                'id' => (int) $r->id,
                'name' => (string) $r->name,
                'stock' => (int) $r->count,
            ])
            ->toArray();
    }

    protected function getStock(int $productId, ?int $sizeId): ?int
    {
        if (!$sizeId)
            return null; // безразмерный товар
        $row = DB::table('product_sizes')
            ->where('product_id', $productId)
            ->where('size_id', $sizeId)
            ->value('count');

        return $row === null ? 0 : (int) $row;
    }


    public function scan(): void
    {
        $code = trim((string) $this->barcode);
        if ($code === '')
            return;

        // 1) Пытаемся обработать как код (barcode/sku варианта, sku товара, id)
        if ($this->tryAddByCode($code)) {
            // успешно добавили или показали выбор размеров
            return;
        }

        // 2) Если не распознали как код — ищем по имени
        $this->results = $this->searchByName($code);
        if (empty($this->results)) {
            \Filament\Notifications\Notification::make()
                ->title('Ничего не найдено')
                ->danger()->send();
        }
    }

    /**
     * Пробуем интерпретировать ввод как КОД:
     * - product_sizes.barcode или product_sizes.sku → сразу кладём с size_id
     * - products.sku → добавляем (1 размер сразу, >1 показать выбор)
     * - products.id (цифры) → аналогично
     * Вернёт true, если код обработан (добавили/показали выбор), иначе false.
     */
    protected function tryAddByCode(string $code): bool
    {
        // --- 1) Вариант (SKU/штрихкод размера)
        $variant = \DB::table('product_sizes as ps')
            ->select('ps.product_id', 'ps.size_id')
            ->where(function ($w) use ($code) {
                $w->where('ps.barcode', $code)
                    ->orWhere('ps.sku', $code);
            })
            ->first();

        if ($variant) {
            $p = \App\Models\Product::withoutGlobalScopes()->withoutTrashed()
                ->select($this->selectFields())
                ->find($variant->product_id);

            if ($p) {
                $this->addToCart((int) $p->id, (int) $variant->size_id);
                $this->barcode = '';
                $this->results = [];
                return true;
            }
        }

        // --- 2) Товар по SKU (без учёта регистра, безопасно к пробелам)
        if (\Schema::hasColumn('products', 'sku')) {
            $normSku = mb_strtolower(trim($code));

            $p = \App\Models\Product::withoutGlobalScopes()
                ->withoutTrashed()
                // PostgreSQL: точное сравнение без регистра
                ->whereRaw('LOWER(TRIM(sku)) = ?', [$normSku])
                ->select($this->selectFields())
                ->first();

            if ($p) {
                $sizes = $this->getSizes((int) $p->id);

                if (count($sizes) > 1) {
                    $this->results = [
                        [
                            'id' => (int) $p->id,
                            'name' => $p->name_ru ?? $p->name ?? ('Товар #' . $p->id),
                            'price' => (int) ($p->price ?? 0),
                            'image' => $this->imageUrl($p),
                            'sizes' => $sizes,
                        ]
                    ];
                    $this->barcode = '';
                    return true;
                } elseif (count($sizes) === 1) {
                    $this->addToCart((int) $p->id, (int) $sizes[0]['id']);
                    $this->barcode = '';
                    $this->results = [];
                    return true;
                } else {
                    $this->addToCart((int) $p->id, null);
                    $this->barcode = '';
                    $this->results = [];
                    return true;
                }
            }
        }

        // --- 3) Товар по ID (числовой код)
        if (ctype_digit($code)) {
            $p = \App\Models\Product::withoutGlobalScopes()->withoutTrashed()
                ->select($this->selectFields())
                ->find((int) $code);

            if ($p) {
                $sizes = $this->getSizes((int) $p->id);
                if (count($sizes) > 1) {
                    $this->results = [
                        [
                            'id' => (int) $p->id,
                            'name' => $p->name_ru ?? $p->name ?? ('Товар #' . $p->id),
                            'price' => (int) ($p->price ?? 0),
                            'image' => $this->imageUrl($p),
                            'sizes' => $sizes,
                        ]
                    ];
                    $this->barcode = '';
                    return true;
                } elseif (count($sizes) === 1) {
                    $this->addToCart((int) $p->id, (int) $sizes[0]['id']);
                    $this->barcode = '';
                    $this->results = [];
                    return true;
                } else {
                    $this->addToCart((int) $p->id, null);
                    $this->barcode = '';
                    $this->results = [];
                    return true;
                }
            }
        }

        // не похоже на код
        return false;
    }

    /**
     * Автопоиск по имени во время ввода.
     * Если ввод похож на код (чистые цифры/длинная строка) — не мешаем скану.
     */
    /** Автопоиск по имени */
    public function updatedBarcode(string $value): void
    {
        $q = trim($value);
        if ($q === '') {
            $this->results = [];
            return;
        }

        // Мы НЕ пытаемся авто-добавливать тут.
        // Подсказки по имени показываем только если код не распознан и длина >=3
        if (mb_strlen($q) < 3) {
            $this->results = [];
            return;
        }

        // Покажем подсказки по имени, но сам add произойдёт в scan()
        $this->results = $this->searchByName($q);
    }

    protected function searchByName(string $name): array
    {
        $q = \App\Models\Product::withoutGlobalScopes()
            ->withoutTrashed()
            ->with(['colors.color:id,name']); // если добавишь hex — добавь ,hex

        if (\Schema::hasColumn('products', 'name_ru')) {
            $q->where('name_ru', 'ILIKE', '%' . $name . '%');
        } elseif (\Schema::hasColumn('products', 'name')) {
            $q->where('name', 'ILIKE', '%' . $name . '%');
        } else {
            return [];
        }

        $select = ['id', 'price'];
        foreach (['name_ru', 'name', 'image'] as $c) {
            if (\Schema::hasColumn('products', $c))
                $select[] = $c;
        }

        return $q->select($select)->limit(20)->get()
            ->map(function ($p) {
                // размеры с остатками
                $sizes = $this->getSizes((int) $p->id); // [{id,name,stock}]
                // суммарное количество по размерам (если sizes есть)
                $qtyTotal = 0;
                foreach ($sizes as $s) {
                    $qtyTotal += (int) ($s['stock'] ?? 0);
                }
                // цвета
                $colors = $p->colors
                    ->map(fn($pc) => [
                        'id' => (int) $pc->color_id,
                        'name' => (string) ($pc->color?->name ?? ''),
                        // 'hex'  => $pc->color?->hex ?? null, // раскомментируй, когда появится колонка hex
                    ])
                    ->filter(fn($c) => $c['name'] !== '')
                    ->values()
                    ->all();

                return [
                    'id' => (int) $p->id,
                    'name' => $p->name_ru ?? $p->name ?? ('Товар #' . $p->id),
                    'price' => (int) ($p->price ?? 0),
                    'image' => $this->imageUrl($p),
                    'sizes' => $sizes,        // для кнопок
                    'colors' => $colors,       // для чипсов
                    'qty_total' => $qtyTotal > 0 ? $qtyTotal : null, // для «Количество»
                ];
            })->toArray();
    }
    /** Подсказки по SKU: сначала варианты (product_sizes.sku ILIKE), затем товары (products.sku ILIKE) */
    /** Подсказки по SKU: сначала варианты (product_sizes.sku ILIKE), затем товары (products.sku ILIKE) */
    protected function searchBySku(string $sku): array
    {
        $out = [];

        // ----- Варианты (product_sizes) -----
        if (\Schema::hasTable('product_sizes') && \Schema::hasColumn('product_sizes', 'sku')) {
            $qb = \DB::table('product_sizes as ps')
                ->join('products as p', 'p.id', '=', 'ps.product_id')
                ->leftJoin('sizes as s', 's.id', '=', 'ps.size_id')
                ->whereNotNull('ps.sku')
                ->where('ps.sku', 'ILIKE', '%' . $sku . '%');

            // Собираем select динамически, чтобы не лезть в несуществующие поля
            $select = [
                'p.id as product_id',
                'p.price',
                'p.image',
                'ps.size_id',
                'ps.sku as variant_sku',
                'ps.barcode as variant_barcode',
                's.name as size_name',
            ];
            if (\Schema::hasColumn('products', 'name_ru'))
                $select[] = 'p.name_ru as product_name_ru';
            if (\Schema::hasColumn('products', 'name'))
                $select[] = 'p.name as product_name';

            $rows = $qb->select($select)->limit(20)->get();

            foreach ($rows as $r) {
                $name = $r->product_name_ru ?? $r->product_name ?? ('Товар #' . $r->product_id);
                $out[] = [
                    'id' => (int) $r->product_id,
                    'name' => $name . ($r->size_name ? (' • ' . $r->size_name) : ''),
                    'price' => (int) ($r->price ?? 0),
                    'image' => $this->imageUrl((object) ['image' => $r->image]),
                    'sizes' => $r->size_id
                        ? [
                            [
                                'id' => (int) $r->size_id,
                                'name' => (string) $r->size_name,
                                'stock' => $this->getStock((int) $r->product_id, (int) $r->size_id),
                            ]
                        ]
                        : [],
                    'sku' => $r->variant_sku,
                    'barcode' => $r->variant_barcode,
                ];
            }
            if (!empty($out))
                return $out;
        }

        // ----- SKU модели (products.sku ILIKE) -----
        if (\Schema::hasColumn('products', 'sku')) {
            $rows = \App\Models\Product::withoutGlobalScopes()->withoutTrashed()
                ->whereNotNull('sku')
                ->where('sku', 'ILIKE', '%' . $sku . '%')
                ->select($this->selectFields())
                ->limit(20)
                ->get();

            foreach ($rows as $p) {
                $out[] = [
                    'id' => (int) $p->id,
                    'name' => $p->name_ru ?? $p->name ?? ('Товар #' . $p->id),
                    'price' => (int) ($p->price ?? 0),
                    'image' => $this->imageUrl($p),
                    'sizes' => $this->getSizes((int) $p->id),
                ];
            }
        }

        return $out;
    }
    // добавь поле состояния
    public array $selectedColors = []; // [productId => colorId]

    // выбрать цвет в карточке результатов
    public function selectColor(int $productId, int $colorId): void
    {
        $this->selectedColor[$productId] = $colorId;
    }

    // при добавлении в корзину поднимем color_id + имя
    public function addToCart(int $productId, ?int $sizeId = null, ?int $colorId = null): void
    {
        $product = \App\Models\Product::find($productId);
        if (!$product)
            return;

        $stock = $this->getStock($productId, $sizeId);

        // уже в корзине? — сравниваем по паре (size_id, color_id)
        foreach ($this->cart as &$row) {
            if (
                $row['id'] === $productId
                && ($row['size_id'] ?? null) === $sizeId
                && ($row['color_id'] ?? null) === $colorId
            ) {
                if ($stock !== null && $row['qty'] + 1 > $stock) {
                    \Filament\Notifications\Notification::make()
                        ->title('Недостаточно остатка')
                        ->body('Доступно: ' . $stock . ' шт.')
                        ->danger()->send();
                    return;
                }
                $row['qty']++;
                $this->barcode = '';
                $this->results = [];
                $this->sortCart();
                return;
            }
        }
        unset($row);

        // имена варианта (size / color) и sku варианта
        $sizeName = null;
        $variantSku = null;
        if ($sizeId) {
            $rec = \DB::table('product_sizes')
                ->leftJoin('sizes', 'sizes.id', '=', 'product_sizes.size_id')
                ->select('sizes.name as size_name', 'product_sizes.sku as variant_sku')
                ->where('product_sizes.product_id', $productId)
                ->where('product_sizes.size_id', $sizeId)
                ->first();
            $sizeName = $rec->size_name ?? null;
            $variantSku = $rec->variant_sku ?? null;
        }

        $colorName = null;
        if ($colorId) {
            $colorName = \DB::table('colors')->where('id', $colorId)->value('name');
        }

        $item = $this->productToCartItem($product, $sizeId, $sizeName, $variantSku, $colorId, $colorName);

        if ($stock !== null && $item['qty'] > $stock) {
            \Filament\Notifications\Notification::make()
                ->title('Недостаточно остатка')
                ->body('Доступно: ' . $stock . ' шт.')
                ->danger()->send();
            return;
        }

        $this->cart[] = $item;
        $this->barcode = '';
        $this->results = [];
        $this->sortCart();
    }



    // чтобы узнать, есть ли цвета (используется в addSelected)
    protected function getColors(int $productId): array
    {
        // product_colors(product_id,color_id) + colors(id,name)
        if (!\Schema::hasTable('product_colors') || !\Schema::hasTable('colors'))
            return [];
        return \DB::table('product_colors as pc')
            ->join('colors as c', 'c.id', '=', 'pc.color_id')
            ->where('pc.product_id', $productId)
            ->select('c.id', 'c.name')
            ->orderBy('c.name')
            ->get()
            ->map(fn($r) => ['id' => (int) $r->id, 'name' => (string) $r->name])
            ->toArray();
    }

    // расширим item


    protected function productToCartItem(object $p, ?int $sizeId = null, ?string $sizeName = null, ?string $variantSku = null): array
    {
        return [
            'id' => (int) $p->id,
            'name' => $p->name_ru ?? $p->name ?? ('Товар #' . $p->id),
            'price' => (int) ($p->price ?? 0),
            'qty' => 1,
            'image' => $this->imageUrl($p),
            'size_id' => $sizeId,
            'size_name' => $sizeName,              // 👈 добавили
            'sku' => $variantSku ?? $p->sku, // 👈 сначала SKU варианта, потом SKU товара
        ];
    }

    /** Сортировка корзины: по названию, затем по размеру */
    protected function sortCart(): void
    {
        usort($this->cart, function ($a, $b) {
            $n = strcmp(mb_strtolower($a['name']), mb_strtolower($b['name']));
            if ($n !== 0)
                return $n;
            return strcmp((string) ($a['size_name'] ?? ''), (string) ($b['size_name'] ?? ''));
        });
        // переиндексируем
        $this->cart = array_values($this->cart);
    }


    protected function imageUrl(object $p): ?string
    {
        if (!Schema::hasColumn('products', 'image') || empty($p->image))
            return null;
        return str_starts_with($p->image, 'http') ? $p->image : asset('storage/' . $p->image);
    }

    public function inc(int $i): void
    {
        $this->cart[$i]['qty']++;
    }

    public function dec(int $i): void
    {
        if ($this->cart[$i]['qty'] > 1)
            $this->cart[$i]['qty']--;
        else
            $this->remove($i);
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
    public function discount(): int
    {
        return 0;
    }
    public function tax(): int
    {
        return 0;
    }
    public function total(): int
    {
        return $this->subtotal() - $this->discount() + $this->tax();
    }

    // ---------------- helpers ----------------

    /** Только barcode/sku/id — БЕЗ поиска по имени */
    /** Ищем только по коду: variant.barcode, variant.sku, product.sku, numeric id */

    private function normalizePhone(?string $raw): ?string
    {
        if (!$raw)
            return null;
        $digits = preg_replace('/\D+/', '', $raw);
        // если без кода страны и начинается с 9xx..., добавим 998
        if (strlen($digits) === 9) {
            $digits = '998' . $digits;
        }
        // допустим 12 цифр для UZ (998XXXXXXXXX)
        return strlen($digits) >= 9 ? $digits : null;
    }
    private function ensureCustomer(): ?\App\Models\User
    {
        // если уже выбран конкретный user_id — используем его
        if ($this->customerId) {
            return \App\Models\User::find($this->customerId);
        }

        // иначе пробуем по телефону
        $phone = $this->normalizePhone($this->customerPhone);
        if (!$phone) {
            return null; // продажа без клиента — допустимо
        }

        $user = \App\Models\User::where('phone', $phone)->first();

        if ($user) {
            // синхронизируем имя, если ввели новое
            if ($this->customerName && empty($user->full_name)) {
                $user->full_name = $this->customerName;
                $user->save();
            }
            $this->customerId = $user->id;
            return $user;
        }

        // создаём нового «гостя» для POS
        $user = \App\Models\User::create([
            'phone' => $phone,
            'full_name' => $this->customerName ?: 'POS Client',
            'is_first_order' => true,
            // остальные поля по модели — nullable
        ]);

        $this->customerId = $user->id;

        return $user;
    }

    protected function selectFields(): array
    {
        $fields = ['id', 'price'];
        foreach (['name_ru', 'name', 'image'] as $c) {
            if (Schema::hasColumn('products', $c))
                $fields[] = $c;
        }
        return $fields;
    }

    public function checkout(): void
    {
        // 1) Валидация содержимого в зависимости от режима
        if ($this->mode === 'sale' && empty($this->cart)) {
            Notification::make()->title('Корзина пуста')->danger()->send();
            return;
        }
        if (in_array($this->mode, ['return', 'exchange'])) {
            // должен быть выбран исходный чек и хотя бы одна позиция к возврату (>0)
            $hasReturn = false;
            foreach ($this->returnLines as $r) {
                if ((int) ($r['count'] ?? 0) > 0) {
                    $hasReturn = true;
                    break;
                }
            }
            if (!$hasReturn) {
                Notification::make()->title('Не выбраны позиции для возврата')->danger()->send();
                return;
            }
        }

        // 2) Клиент: найдём/создадим по телефону как и раньше
        $customer = $this->ensureCustomer();
        $userId = $customer?->id;

        // 3) Подготовим позиции продажи (из корзины)
        $itemsSale = array_map(function (array $row) {
            return [
                'product_id' => (int) $row['id'],
                'size_id' => isset($row['size_id']) ? (int) $row['size_id'] : null,
                'color_id' => $row['color_id'] ?? null,
                'count' => (int) $row['qty'],
                'price' => (int) $row['price'],
            ];
        }, $this->cart);

        // 4) Позиции возврата (из исходного чека)
        $itemsReturn = [];
        if (in_array($this->mode, ['return', 'exchange'])) {
            foreach ($this->returnLines as $r) {
                $c = (int) ($r['count'] ?? 0);
                $max = (int) ($r['max'] ?? 0);
                if ($c > 0 && $c <= $max) {
                    $itemsReturn[] = [
                        'original_order_id' => $r['original_order_id'] ?? null,
                        'product_id' => (int) $r['product_id'],
                        'size_id' => $r['size_id'] ?? null,
                        'color_id' => $r['color_id'] ?? null,
                        'count' => $c,
                        'price' => (int) $r['price'],
                    ];
                }
            }
        }

        try {
            /** @var \App\Services\Sales\OrderWriter $writer */
            $writer = app(\App\Services\Sales\OrderWriter::class);

            $locationId = $this->locationId
                ?? \DB::table('stock_locations')->where('code', 'store_1')->value('id');

            // найдём id исходного чека для связки (если указан номер/ид)
            $originalGroupId = null;
            if ($this->originalNumber && in_array($this->mode, ['return', 'exchange'])) {
                $originalGroupId = \App\Models\OrderGroup::where('order_number', $this->originalNumber)
                    ->orWhere('id', (int) $this->originalNumber)
                    ->value('id');
            }

            // Собираем payload под наш OrderWriter
            $payload = [
                'type' => $this->mode,     // sale | return | exchange
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

            // 5) Очистка
            $this->clearCart();
            $this->barcode = '';
            $this->results = [];
            $this->returnLines = [];
            $this->originalNumber = null;

            // 6) Уведомление
            $label = match ($this->mode) {
                'return' => 'Возврат оформлен',
                'exchange' => 'Обмен оформлен',
                default => 'Продажа проведена',
            };

            Notification::make()
                ->title($label)
                ->body('Чек № ' . ($group->order_number ?? $group->id) . ' • Сумма: ' . number_format((int) ($group->total ?? 0)) . ' сум')
                ->success()
                ->send();

            // 7) Телеграм (POS бот)
            $total = (int) ($group->total ?? 0);
            $orderNo = $group->order_number ?? $group->id;
            $cashier = auth()->user()->full_name
                ?? auth()->user()->name
                ?? ('ID:' . (auth()->id() ?? '—'));
            $caption = match ($this->mode) {
                'return' => 'ВОЗВРАТ',
                'exchange' => 'ОБМЕН',
                default => 'POS продажа',
            };

            $msg = "🧾 <b>{$caption}</b>\n"
                . "Чек № <b>{$orderNo}</b>\n"
                . "Сумма: <b>" . number_format($total, 0, '.', ' ') . "</b> сум\n"
                . "Оплата: <b>" . ($this->paymentMethod ?? '—') . "</b>\n"
                . "Кассир: <b>{$cashier}</b>"
                . ($customer ? ("\nКлиент: <b>" . e($customer->full_name ?: $customer->phone) . "</b>") : '');

            app(\App\Services\PosBotService::class)->send(env('POS_TELEGRAM_CHAT_ID'), $msg);

            // Обновим «Последние продажи»
            $this->loadRecent();

        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Ошибка при оформлении')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }



}