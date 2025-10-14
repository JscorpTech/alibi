<?php

namespace App\Filament\Clusters\Products\Resources;

use App\Enums\GenderEnum;
use App\Enums\ProductStatusEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Clusters\Products;
use Filament\Resources\Resource;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use App\Models\Size;
use App\Models\Color;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Set;




use Filament\Forms\Components\Actions as FormActions;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Services\VariantGenerator;
use Filament\Support\Enums\Alignment;      // для выравнивания add-кнопки
use Filament\Support\Enums\ActionSize;     // для размера кнопки

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\{Section, Grid, Toggle, Select, Hidden, Actions, View};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;






class ProductResource extends Resource
{
    protected static ?string $navigationGroup = 'Products';
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $navigationIcon = 'heroicon-o-tag';



    // app/Filament/Resources/ProductResource.php
    public static function canCreate(): bool
    {
        return true;
    }
    public static function canViewAny(): bool
    {
        return true;
    }
    public static function canEdit($record): bool
    {
        return true;
    }
    public static function canDelete($record): bool
    {
        return true;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'id';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }


    public static function getNavigationLabel(): string
    {
        return __('navigation:products');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Общая двухколоночная сетка 8/4
            Forms\Components\Grid::make([
                'default' => 1,
                'lg' => 12,
            ])->schema([

                        // ── Левая колонка (8/12)
                        Forms\Components\Grid::make(['default' => 1])->columnSpan(['lg' => 8])->schema([

                            Forms\Components\Section::make('Основное')
                                ->schema([
                                    Forms\Components\TextInput::make('name_ru')
                                        ->label('Название')
                                        ->required()
                                        ->placeholder('Футболка с коротким рукавом'),
                                    Forms\Components\TextInput::make('label')
                                        ->label('Метка (необязательно)'),
                                    Forms\Components\RichEditor::make('desc_ru')
                                        ->label('Описание')
                                        ->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'bulletList', 'orderedList', 'link'])
                                        ->columnSpanFull()
                                        ->columnSpan(2)
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('СМИ')
                                ->schema([
                                    // Главное фото — как было
                                    Forms\Components\FileUpload::make('image')
                                        ->label('Главное фото')
                                        ->helperText('PNG/JPG/WebP, до 4 МБ.')
                                        ->directory('products')
                                        ->image()
                                        ->imageEditor()
                                        ->panelLayout('compact')
                                        ->imagePreviewHeight('240px')
                                        ->maxSize(4096)
                                        ->acceptedFileTypes(['image/*'])
                                        ->columnSpan(1),


                                    // Галерея — ПРЯМО В products.gallery
                                    Forms\Components\FileUpload::make('gallery')
                                        ->label('Галерея')
                                        ->helperText('Добавьте дополнительные фото. Первое — будет обложкой галереи.')
                                        ->multiple()                // множественная загрузка
                                        ->reorderable()             // можно менять порядок
                                        ->directory('products')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->image()
                                        ->imageEditor()
                                        ->panelLayout('compact')
                                        ->imagePreviewHeight('120px')
                                        ->maxSize(4096)
                                        ->acceptedFileTypes(['image/*'])
                                        ->columnSpan(2),
                                ])
                                ->columns(3)
                                ->columnSpanFull()
                                ->compact(),

                            Forms\Components\Section::make('Фото по цветам')
                                ->schema([
                                    Forms\Components\Actions::make([


                                        Forms\Components\Actions\Action::make('syncColorsFromOptions')
                                            ->label('Синхронизировать с осью Color')
                                            ->icon('heroicon-o-arrow-path')
                                            ->color('gray')
                                            ->action(function (Forms\Get $get, Forms\Set $set) {
                                                $opts = collect($get('variant_state.variant_options') ?? []);
                                                $colors = collect($opts->firstWhere('name', 'Color')['values'] ?? [])
                                                    ->filter()->unique()->values();

                                                // текущее (в виде списка строк репитера)
                                                $rows = collect($get('color_images') ?? []);

                                                foreach ($colors as $c) {
                                                    if (!$rows->firstWhere('color', $c)) {
                                                        $rows->push(['color' => $c, 'paths' => [], 'cover_index' => null]);
                                                    }
                                                }

                                                // удалим строки для убранных цветов
                                                $rows = $rows->filter(fn($r) => in_array($r['color'] ?? null, $colors->all(), true))->values();
                                                $set('color_images', $rows->all());

                                                \Filament\Notifications\Notification::make()
                                                    ->title('Цвета синхронизированы: ' . $colors->count())
                                                    ->success()->send();


                                            }),


                                    ])->columnSpanFull(),



                                    Forms\Components\Repeater::make('color_images')
                                        ->label('Галереи по цветам')
                                        ->schema([
                                            Forms\Components\Select::make('color')
                                                ->label('Цвет')
                                                ->options(\App\Models\Color::pluck('name', 'name')->all())
                                                ->searchable()->preload()->required()->columnSpan(12),

                                            Forms\Components\FileUpload::make('paths')
                                                ->label('Картинки этого цвета')
                                                ->multiple()->reorderable()->live()
                                                ->directory('products')->disk('public')->visibility('public')
                                                ->image()->imageEditor()
                                                ->panelLayout('compact')->imagePreviewHeight('100px')
                                                ->maxSize(4096)->acceptedFileTypes(['image/*'])
                                                ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state) {
                                                    $count = is_countable($state) ? count($state) : 0;
                                                    $idx = (int) ($get('cover_index') ?? 0);
                                                    if ($count === 0) {
                                                        $set('cover_index', null);
                                                    } elseif ($idx < 0 || $idx >= $count) {
                                                        $set('cover_index', 0);
                                                    }
                                                })
                                                ->columnSpan(12),

                                            Forms\Components\Select::make('cover_index')
                                                ->label('Обложка цвета')
                                                ->helperText('Если не выбрано — возьмём первое изображение.')
                                                ->options(function (Forms\Get $get) {
                                                    $paths = (array) ($get('paths') ?? []);
                                                    $count = count($paths);
                                                    if ($count === 0)
                                                        return [];
                                                    return collect(range(0, $count - 1))
                                                        ->mapWithKeys(fn($i) => [$i => 'Фото ' . ($i + 1)])
                                                        ->all();
                                                })
                                                ->disabled(fn(Forms\Get $get) => empty($get('paths')))
                                                ->native(false)->reactive()->columnSpan(12),
                                        ])
                                        ->columns(12)
                                        ->default([])
                                        ->reorderable()
                                        ->collapsible()
                                        ->itemLabel(fn(array $state) => $state['color'] ?? 'Новый цвет')
                                ])
                                ->collapsible()
                                ->compact()
                                ->columnSpanFull(),












                            Forms\Components\Section::make('Цена')
                                ->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->numeric()->required()->prefix('сум')
                                        ->label('Цена'),
                                    Forms\Components\TextInput::make('discount')
                                        ->numeric()->default(0)->prefix('%')
                                        ->label('Скидка'),
                                    Forms\Components\TextInput::make('cost_price')
                                        ->default(0)->prefix('сум')
                                        ->label('Себестоимость')
                                        ->helperText('Внутренний учёт, не видно клиенту.'),

                                ])->columns(3),

                            Forms\Components\Section::make('Организация')
                                ->extraAttributes(['class' => 'overflow-visible'])   // 👈 важно
                                ->schema([
                                    Forms\Components\Select::make('categories')
                                        ->label('Категории')
                                        ->relationship('categories', 'name_ru')
                                        ->native(),       // ⟵ системный селект, всегда поверх, но без поиска

                                    Forms\Components\Select::make('subcategories')
                                        ->relationship('subcategories', 'name_ru')
                                        ->native()
                                        ->label('Подкатегории'),

                                    Forms\Components\Select::make('brand')
                                        ->relationship('brand', 'name')

                                        ->native()
                                        ->label('Бренд'),

                                    Forms\Components\Select::make('tags')
                                        ->relationship('tags', 'name')
                                        ->native()
                                        ->label('Теги'),
                                ])
                                ->columns(2),











                            Forms\Components\Section::make('Варианты')
                                ->schema([
                                    // Плейсхолдер (показывается, когда нет осей)
                                    // Forms\Components\View::make('filament.products.variant-placeholder')
                                    //     ->visible(fn(Forms\Get $get) => empty($get('variant_options')))
                                    //     ->columnSpanFull(),



                                    // Repeater с осями
                                    Forms\Components\Repeater::make('variant_options')
                                        ->label('')
                                        ->default([])
                                        // Кастомизируем кнопку "Добавить"
                                        ->addAction(
                                            fn(Action $action) =>
                                            $action
                                                ->icon('heroicon-m-plus')
                                                ->label(fn(Get $get): string => empty($get('variant_options'))
                                                    ? 'Добавьте такие параметры, как размер или цвет'
                                                    : 'Добавить ещё один вариант')
                                                ->button()
                                                ->extraAttributes([
                                                    'class' => 'text-xs px-3 py-1.5 rounded-lg text-gray-700 hover:bg-gray-100 transition',
                                                ])
                                        )
                                        ->addActionAlignment(Alignment::Start) // выравнивание слева
                                        ->collapsible(false)
                                        ->columns(12)
                                        ->live()
                                        ->schema([
                                            Forms\Components\Select::make('name')
                                                ->label('Название варианта')
                                                ->options(['Size' => 'Размер', 'Color' => 'Цвета'])
                                                ->required()
                                                ->native(false)
                                                ->columnSpan(12)
                                                ->reactive()
                                                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('values', [])),

                                            Forms\Components\Select::make('values')
                                                ->label('Значения')
                                                ->multiple()
                                                ->searchable()
                                                ->preload()
                                                ->reactive()
                                                ->columnSpan(12)
                                                ->options(function (Forms\Get $get) {
                                                    return match ($get('name')) {
                                                        'Size' => \App\Models\Size::pluck('name', 'name')->all(),
                                                        'Color' => \App\Models\Color::pluck('name', 'name')->all(),
                                                        default => [],
                                                    };
                                                })
                                                ->required()
                                                ->hidden(fn(Forms\Get $get) => blank($get('name'))),



                                            // 👇 Кнопка "Готово" внутри ЭЛЕМЕНТА репитера — сразу под полем "Значения", справа
                                            FormActions::make([
                                                Action::make('doneOptions')
                                                    ->label('Готово')
                                                    ->icon('heroicon-o-check')
                                                    ->button()
                                                    ->size(ActionSize::Small)
                                                    ->extraAttributes([
                                                        'class' => 'text-white px-3 py-1.5 rounded-lg text-sm focus:ring-0 focus:outline-none border border-transparent transition',
                                                        'style' => 'background-color: #000; color: #fff;',
                                                    ])
                                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                                        // 1) читаем все оси из корня репитера
                                                        $opts = collect($get('../../variant_options') ?? [])
                                                            ->filter(fn($o) => !empty($o['name']) && !empty($o['values']))
                                                            ->values()
                                                            ->all();

                                                        if (empty($opts)) {
                                                            $set('variants_draft', []);
                                                            $set('variants_editor', []);
                                                            $set('stocks', []); // очистим карту количеств
                                                            return;
                                                        }

                                                        // 2) декартово произведение значений осей -> список attrs
                                                        $result = [[]];
                                                        foreach ($opts as $opt) {
                                                            $tmp = [];
                                                            foreach ($result as $r) {
                                                                foreach ((array) $opt['values'] as $val) {
                                                                    $tmp[] = array_merge($r, [$opt['name'] => $val]);
                                                                }
                                                            }
                                                            $result = $tmp;
                                                        }

                                                        // 3) старые количества, чтобы не потерять введённые ранее
                                                        $oldStocks = (array) ($get('../../stocks') ?? []);

                                                        // мини-хелпер: ключ строки (id:ID или attrs:HASH)
                                                        $keyOf = function (array $row): string {
                                                            if (!empty($row['id'])) {
                                                                return 'id:' . (int) $row['id'];
                                                            }
                                                            $attrs = (array) ($row['attrs'] ?? []);
                                                            ksort($attrs);
                                                            return 'attrs:' . substr(md5(json_encode($attrs, JSON_UNESCAPED_UNICODE)), 0, 12);
                                                        };

                                                        // 4) собираем строки редактора + карту stocks
                                                        $rows = [];
                                                        $stocks = [];

                                                        foreach ($result as $attrs) {
                                                            // нормализуем и делаем title
                                                            ksort($attrs);
                                                            $title = implode(' / ', array_map(
                                                                fn($k, $v) => "{$k}: {$v}",
                                                                array_keys($attrs),
                                                                array_values($attrs)
                                                            ));

                                                            $row = [
                                                                'title' => $title,
                                                                'attrs' => $attrs,
                                                                'price' => 0,
                                                                'stock' => 0,
                                                                'available' => true,
                                                                'sku' => null,
                                                            ];
                                                            $rows[] = $row;

                                                            // ключ для stocks и перенос старого значения если было
                                                            $rk = $keyOf($row);
                                                            $stocks[$rk] = isset($oldStocks[$rk]) ? (int) $oldStocks[$rk] : 0;
                                                        }

                                                        // 5) записываем в состояние редактора
                                                        $set('../../variants_draft', $rows);
                                                        $set('../../variants_editor', $rows);
                                                        $set('../../stocks', $stocks); // 👈 важное: карта количеств
                                            
                                                        \Filament\Notifications\Notification::make()
                                                            ->title('Варианты созданы: ' . count($rows))
                                                            ->success()
                                                            ->send();
                                                    }),
                                            ])
                                                ->alignment('right')
                                                ->visible(true)
                                                ->extraAttributes(['class' => 'mt-2'])
                                                ->columnSpan(12),
                                        ]),



                                    Forms\Components\View::make('filament.products.variant-existing')
                                        ->visible(fn($record) => (bool) $record)
                                        ->reactive()
                                        ->viewData([
                                            'variants' => fn($record) => $record
                                                ? $record->variants()
                                                    ->orderByDesc('id')
                                                    ->get(['id', 'sku', 'price', 'stock', 'available', 'attrs', 'barcode'])
                                                    ->values()
                                                    ->toArray()
                                                    ->map(function ($v) use ($record) {
                                                        $attrs = (array) $v->attrs;

                                                        // мини-хелпер для абсолютного URL
                                                        $toUrl = function (?string $p): ?string {
                                                            if (!$p)
                                                                return null;
                                                            return str_starts_with($p, 'http') ? $p : \Storage::url($p);
                                                        };

                                                        $color = $attrs['Color'] ?? null;
                                                        $cover = null;

                                                        if ($color && !empty($record->color_images[$color])) {
                                                            $ci = $record->color_images[$color];
                                                            $cover = is_array($ci) ? ($ci[0] ?? null) : $ci;
                                                        }

                                                        if (!$cover) {
                                                            $gallery = is_array($record->gallery) ? $record->gallery : [];
                                                            $cover = $gallery[0] ?? $record->image ?? null;
                                                        }

                                                        $cover = $toUrl($cover);

                                                        $attrsText = $attrs
                                                            ? implode(' • ', array_map(fn($k, $val) => "{$k}: {$val}", array_keys($attrs), array_values($attrs)))
                                                            : '—';

                                                        return [
                                                            'id' => $v->id,
                                                            'title' => $attrsText,
                                                            'attrs' => $attrsText,
                                                            'sku' => $v->sku,
                                                            'barcode' => (string) ($v->barcode ?? ''), // 👈 явно строка
                                                            'price' => (int) ($v->price ?? 0),
                                                            'stock' => (int) ($v->stock ?? 0),
                                                            'available' => (bool) $v->available,
                                                            'cover' => $cover,
                                                        ];
                                                    })

                                                : [],
                                        ])
                                        ->columnSpanFull(),

                                    Forms\Components\Grid::make(12)->schema([
                                        Forms\Components\Select::make('group_by')
                                            ->label('Группировать по')
                                            ->options(['Size' => 'Size', 'Color' => 'Color'])
                                            ->native(false)
                                            ->reactive()
                                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => $set('filter_value', null) | $this->rebuildVariantGroups($get, $set))
                                            ->columnSpan(3),

                                        Forms\Components\Select::make('filter_value')
                                            ->label('Значение')
                                            ->options(function (Forms\Get $get) {
                                                $rows = (array) ($get('variants_editor') ?? []);
                                                $group = (string) ($get('group_by') ?? '');
                                                if ($group === '')
                                                    return [];
                                                return collect($rows)->pluck("attrs.$group")->filter()->unique()->sort()->mapWithKeys(fn($v) => [$v => $v])->all();
                                            })
                                            ->native(false)
                                            ->reactive()
                                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => $this->rebuildVariantGroups($get, $set))
                                            ->columnSpan(4),

                                        Forms\Components\TextInput::make('search')
                                            ->label('Поиск')
                                            ->placeholder('SKU, размер, цвет…')
                                            ->reactive()
                                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => $this->rebuildVariantGroups($get, $set))
                                            ->columnSpan(5),

                                        Forms\Components\Hidden::make('variants_grouped')->default([])->dehydrated(false)->reactive(),
                                    ])
                                        ->visible(fn(Forms\Get $get) => filled($get('variants_editor')))
                                        ->columnSpanFull(),


                                    Forms\Components\View::make('filament.products.variant-list-grouped')
                                        ->visible(fn(Forms\Get $get) => filled($get('variants_editor')))
                                        ->viewData(fn(Forms\Get $get) => [
                                            'rows' => $get('variants_filtered') ?: ($get('variants_editor') ?? []),
                                            'groupBy' => (string) ($get('group_by') ?? 'Size'),
                                            'stocks' => (array) ($get('../../stocks') ?? []),  // 👈 ИСПРАВЛЕНО: используем ../../stocks
                                        ])
                                        ->reactive()
                                        ->columnSpanFull(),

                                    Forms\Components\Repeater::make('variants_editor')
                                        ->visible(false)
                                        ->default(fn(Forms\Get $get) => $get('variants_editor') ?? [])
                                        ->dehydrated(true)
                                        ->reactive()
                                        ->schema([]),

                                    Forms\Components\Hidden::make('variants_draft')->default([])->dehydrated(false)->reactive(),




                                ])
                                // важно: все ключи внутри этой секции будут под variant_state.*
                                ->statePath('variant_state')
                                ->columnSpanFull(),

                            Forms\Components\Hidden::make('variant_state.stocks')
                                ->default([])
                                ->dehydrated(false)
                                ->reactive(),



                        ]),



                        // ── Правая колонка (4/12) — «боковая панель»
                        Forms\Components\Grid::make(['default' => 1])
                            ->columnSpan(['lg' => 4])
                            ->extraAttributes(['class' => 'lg:sticky lg:top-6'])
                            ->schema([

                                Forms\Components\Section::make('Публикация')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Активен')
                                            ->helperText('Если выключен — товар скрыт в приложении.'),
                                        Forms\Components\Select::make('channel')
                                            ->options(['online' => 'Онлайн', 'warehouse' => 'Склад'])
                                            ->default('warehouse')
                                            ->label('Канал публикации'),
                                        Forms\Components\Select::make('gender')
                                            ->options([
                                                \App\Enums\GenderEnum::MALE => 'Мужской',
                                                \App\Enums\GenderEnum::FEMALE => 'Женский',
                                            ])->required()->label('Гендер'),
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                \App\Enums\ProductStatusEnum::AVAILABLE => 'В наличии',
                                                \App\Enums\ProductStatusEnum::EXPECTED => 'Ожидается',
                                                \App\Enums\ProductStatusEnum::NOT_AVAILABLE => 'Нет в наличии',
                                            ])->required()->label('Статус'),
                                    ]),

                                Forms\Components\Section::make('Склад')
                                    ->schema([
                                        Forms\Components\Select::make('stock_location_id')
                                            ->relationship('stockLocation', 'name')
                                            ->label('Склад / Магазин')
                                            ->native()       // 👈 системный селект
                                            ->preload()      // подгрузит список сразу
                                    ]),

                                Forms\Components\Section::make('SEO (черновик)')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')->label('Meta title')->maxLength(70),
                                        Forms\Components\Textarea::make('meta_description')->label('Meta description')->rows(3)->maxLength(160),
                                    ]),
                            ]),
                    ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label(__('id')),
                Tables\Columns\ImageColumn::make('image')->width(50)->height(50),
                Tables\Columns\TextColumn::make('name_ru')->label(__('name')),
                Tables\Columns\TextColumn::make('label')->label(__('label')),
                Tables\Columns\TextColumn::make('price')->sortable()->label(__('price')),
                Tables\Columns\TextColumn::make('discount')->sortable()->label(__('discount')),
                Tables\Columns\BadgeColumn::make('channel')->label('Канал')

                    ->colors([
                        'success' => fn($state) => $state === 'online',
                        'gray' => fn($state) => $state === 'warehouse',
                    ])
                    ->formatStateUsing(fn(string $state) => $state === 'online' ? 'Онлайн' : ($state === 'warehouse' ? 'Склад' : ucfirst($state))),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Активен'),
                Tables\Columns\TextColumn::make('created_at')->sortable()->label(__('created_at')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // ← покажет «Все / Только удалённые / Без удалённых»
                Tables\Filters\SelectFilter::make('channel')
                    ->options(['online' => 'Онлайн', 'warehouse' => 'Склад'])
                    ->label('Канал'),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // 1) Печать с формой (qty + by_sizes) — редирект из action
                Tables\Actions\Action::make('printOne')
                    ->label('Печать этикетки')
                    ->icon('heroicon-o-printer')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('qty')->numeric()->default(1)->minValue(1)->label('Копий'),
                        \Filament\Forms\Components\Toggle::make('by_sizes')->label('Отдельно по размерам'),
                    ])
                    ->url(fn($record, $data) => route('print.barcode', [
                        'product' => $record->id,
                        'qty' => $data['qty'] ?? 1,
                        'by_sizes' => (int) ($data['by_sizes'] ?? 0),
                    ]))
                    ->openUrlInNewTab(),



                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->successNotificationTitle('Товар удалён навсегда'),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make()->requiresConfirmation(),
                    Tables\Actions\RestoreBulkAction::make(),

                    // (опционально) массовая печать «по остаткам»
                    Tables\Actions\BulkAction::make('printSelected')
                        ->label('Печать (выбранные)')
                        ->icon('heroicon-o-printer')
                        ->action(function (array $records) {
                            $ids = collect($records)->pluck('id')->implode(',');
                            $url = route('print.barcodes', ['ids' => $ids]);
                            \Filament\Notifications\Notification::make()
                                ->title('Открой печать в новой вкладке')
                                ->body("<a href=\"{$url}\" target=\"_blank\">Печать по остаткам</a>")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts::route('/'),
            'create' => \App\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct::route('/create'),
            'edit' => \App\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct::route('/{record}/edit'),
            // 'sales' => \App\Filament\Clusters\Products\Resources\ProductResource\Pages\ProductSalesReport::route('/{record}/sales-report'),
        ];
    }

    // ВНИЗУ ProductResource (или в отдельный трейт) — helper для сборки групп
    public static function buildVariantsGrouped(array $rows, string $groupBy, string $filterValue, string $q): array
    {
        // помечаем исходные индексы
        $withIdx = [];
        foreach ($rows as $i => $r) {
            $withIdx[] = $r + ['_i' => $i];
        }

        // фильтры
        $filtered = collect($withIdx)->filter(function ($r) use ($groupBy, $filterValue, $q) {
            if ($groupBy && $filterValue !== '') {
                $v = (string) ($r['attrs'][$groupBy] ?? '');
                if (strcasecmp($v, $filterValue) !== 0)
                    return false;
            }
            if ($q !== '') {
                $hay = mb_strtolower(($r['title'] ?? '') . ' ' . ($r['sku'] ?? ''));
                if (!str_contains($hay, mb_strtolower($q)))
                    return false;
            }
            return true;
        });

        // сортировка
        $sorted = $filtered->sort(function ($a, $b) use ($groupBy) {
            if ($groupBy) {
                $ga = (string) ($a['attrs'][$groupBy] ?? '');
                $gb = (string) ($b['attrs'][$groupBy] ?? '');
                if ($ga !== $gb)
                    return strcmp($ga, $gb);
            }
            return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
        })->values();

        // нет группировки — одна общая группа
        if ($groupBy === '') {
            return [
                [
                    'key' => 'Все',
                    'items' => $sorted->map(fn($r) => [
                        'idx' => $r['_i'],
                        'title' => (string) ($r['title'] ?? 'Вариант'),
                        'sku' => (string) ($r['sku'] ?? ''),
                    ])->all(),
                ]
            ];
        }

        // группируем по оси
        return $sorted->groupBy(fn($r) => (string) ($r['attrs'][$groupBy] ?? '—'))
            ->map(function ($items, $key) {
                return [
                    'key' => (string) $key,
                    'items' => $items->map(fn($r) => [
                        'idx' => $r['_i'],
                        'title' => (string) ($r['title'] ?? 'Вариант'),
                        'sku' => (string) ($r['sku'] ?? ''),
                    ])->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function rebuildVariantGroups(Forms\Get $get, Forms\Set $set): void
    {
        $rows = (array) ($get('variants_editor') ?? []);
        $group = (string) ($get('group_by') ?? '');
        $value = (string) ($get('filter_value') ?? '');
        $q = mb_strtolower((string) ($get('search') ?? ''));

        $groups = [];
        foreach ($rows as $r) {
            // фильтры
            if ($group !== '' && (!isset($r['attrs'][$group]) || ($value !== '' && strcasecmp((string) $r['attrs'][$group], $value) !== 0))) {
                continue;
            }
            if ($q !== '') {
                $hay = mb_strtolower(($r['title'] ?? '') . ' ' . ($r['sku'] ?? ''));
                if (!str_contains($hay, $q))
                    continue;
            }

            $key = $group !== '' ? (string) ($r['attrs'][$group] ?? '—') : '—';
            $groups[$key][] = $r;
        }

        // сортировка групп и элементов
        ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);
        foreach ($groups as &$items) {
            usort($items, fn($a, $b) => strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? '')));
        }
        $set('variants_grouped', $groups);
    }

    private static function autoRebuildVariants(Forms\Get $get, Forms\Set $set): void
    {
        $opts = collect($get('variant_options') ?? [])
            ->filter(fn($o) => !empty($o['name']) && !empty($o['values']))
            ->values()->all();

        if (empty($opts)) {
            $set('variants_draft', []);
            $set('variants_editor', []);
            $set('variants_filtered', []);
            return;
        }

        $result = [[]];
        foreach ($opts as $opt) {
            $tmp = [];
            foreach ($result as $r) {
                foreach ((array) $opt['values'] as $val) {
                    $tmp[] = array_merge($r, [$opt['name'] => $val]);
                }
            }
            $result = $tmp;
        }

        $rows = [];
        foreach ($result as $attrs) {
            $title = implode(' / ', array_map(fn($k, $v) => "{$k}: {$v}", array_keys($attrs), array_values($attrs)));
            $rows[] = [
                'title' => $title,
                'attrs' => $attrs,
                'price' => 0,
                'stock' => 0,
                'available' => true,
                'sku' => null,
            ];
        }

        $set('variants_draft', $rows);
        $set('variants_editor', $rows);
        $set('variants_filtered', $rows);
    }
}
