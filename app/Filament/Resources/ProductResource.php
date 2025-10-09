<?php

namespace App\Filament\Resources;

use App\Enums\GenderEnum;
use App\Enums\ProductStatusEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use App\Models\Size;
use App\Models\Color;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions;
use Filament\Forms\Set;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Services\VariantGenerator;
use App\Filament\Resources\HtmlString;

use Filament\Forms\Components\Grid;

use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


use Filament\Forms\Components\TagsInput;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Maxsulotlar';

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
                                    // 📸 Главное фото — компактное
                                    Forms\Components\FileUpload::make('image')
                                        ->label('Главное фото')                                // ✅ чёткий label
                                        ->helperText('PNG/JPG/WebP, до 4 МБ. Перетащите файл или нажмите на плюс.')
                                        ->directory('products')
                                        ->image()
                                        ->imageEditor()
                                        // ->panelAspectRatio('1:1')                           // 🔕 убрать, чтобы не тянуло вверх
                                        ->panelLayout('compact')
                                        ->imagePreviewHeight('240px')                          // ✅ одна привязка по высоте
                                        ->maxSize(4096)
                                        ->acceptedFileTypes(['image/*'])
                                        ->extraAttributes([
                                            'data-plus' => '1',
                                            'style' => '--box-h:240px',                    // совпадает с imagePreviewHeight
                                            'title' => 'Нажмите, чтобы загрузить главное фото', // ✅ нативный tooltip при hover
                                        ])
                                        ->columnSpan(1),

                                    // 🖼️ Галерея — без текста
                                    Forms\Components\Repeater::make('images')
                                        ->label('Галерея')
                                        ->helperText('Добавьте дополнительные фото товара. Первое — главное в галерее.')
                                        ->relationship('images')
                                        ->simple(
                                            Forms\Components\FileUpload::make('path')
                                                ->label('Файл')                                 // внутри simple можно скрыть визуально:
                                                ->hiddenLabel()                                 // ← прячем, чтобы карточка была чистой
                                                ->directory('products')
                                                ->image()
                                                ->imageEditor()
                                                // ->panelAspectRatio('1:1')                    // 🔕 не смешиваем
                                                ->panelLayout('compact')
                                                ->imagePreviewHeight('120px')
                                                ->maxSize(4096)
                                                ->acceptedFileTypes(['image/*'])
                                                ->disk('public')
                                                ->visibility('public')
                                                ->extraAttributes([
                                                    'data-plus' => '1',
                                                    'style' => '--box-h:120px',
                                                    'title' => 'Добавить фото в галерею',  // нативный tooltip
                                                ])
                                        )
                                        ->grid(3)
                                        ->minItems(0)
                                        ->defaultItems(0)
                                        ->addActionLabel('Добавить фото')                       // читаемая подпись
                                        ->addAction(
                                            fn($action) =>
                                            $action->icon('heroicon-o-plus')
                                                ->tooltip('Добавить ещё изображение')        // ✅ tooltip на кнопке
                                        )
                                        ->reorderable()
                                        ->collapsed(false)
                                        ->columnSpan(2)
                                        ->addActionAlignment('start')
                                ])
                                ->columns(3)
                                ->columnSpanFull()
                                ->compact(),
                            Forms\Components\Section::make('Цена')
                                ->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->numeric()->required()->prefix('сум')
                                        ->label('Цена'),
                                    Forms\Components\TextInput::make('discount')
                                        ->numeric()->default(0)->prefix('%')
                                        ->label('Скидка'),
                                    Forms\Components\TextInput::make('cost_price')
                                        ->numeric()->default(0)->prefix('сум')
                                        ->label('Себестоимость')
                                        ->helperText('Внутренний учёт, не видно клиенту.'),
                                    Forms\Components\TextInput::make('sku')
                                        ->maxLength(64)
                                        ->unique(ignoreRecord: true)
                                        ->label('SKU (артикул)'),
                                ])->columns(4),

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

                            // === 1) Варианты (оси) ===

                            Forms\Components\Section::make('Варианты (размеры и остатки)')
                                ->schema([
                                    Forms\Components\Repeater::make('productSizes')
                                        ->relationship('productSizes')
                                        ->label('Размеры и остатки')
                                        ->addActionLabel('Добавить размер')
                                        ->collapsible(false)
                                        ->reorderable(false)
                                        ->cloneable(false)
                                        ->itemLabel(false)
                                        ->defaultItems(0)
                                        ->columns(12) // 👈 одна «полоса» на 12 колонок
                                        ->schema([
                                            Forms\Components\Select::make('size_id')
                                                ->relationship('size', 'name')
                                                ->native()
                                                ->required()
                                                ->label('Размер')
                                                ->columnSpan(4),          // |---- size ----|

                                            Forms\Components\TextInput::make('count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->default(0)
                                                ->label('Остаток')
                                                ->columnSpan(2),          // |-- count --|

                                            Forms\Components\TextInput::make('sku')
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->label('SKU варианта')
                                                ->placeholder('Автогенерация')
                                                ->columnSpan(3),          // |--- sku ---|

                                            Forms\Components\TextInput::make('barcode')
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->label('Barcode (EAN-13)')
                                                ->placeholder('Автогенерация')
                                                ->columnSpan(3),          // |--- barcode ---|
                                        ])
                                        // центрируем по вертикали и добавляем зазор между колонками
                                        ->extraAttributes(['class' => 'items-center gap-3']),
                                ]),


                            Forms\Components\Section::make('Цвета')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Repeater::make('colors')
                                        ->relationship('colors')
                                        ->addActionLabel('Добавить цвет')
                                        ->reorderable()
                                        ->defaultItems(0)
                                        ->grid(1)
                                        ->columns(1)
                                        ->cloneable(false)
                                        ->schema([
                                            Forms\Components\Grid::make(12)
                                                ->extraAttributes(['class' => 'items-center gap-3']) // всё по одной линии, плотнее
                                                ->schema([
                                                    // 🖼 превью слева (64px)
                                                    Forms\Components\FileUpload::make('path')
                                                        ->directory('products')
                                                        ->image()
                                                        ->imageEditor()
                                                        ->panelAspectRatio('1:1')
                                                        ->panelLayout('compact')
                                                        ->imagePreviewHeight('120px')
                                                        ->maxSize(4096)
                                                        ->label('')                           // лучше пустую строку, не false
                                                        ->acceptedFileTypes(['image/*'])
                                                        ->disk('public')
                                                        ->visibility('public')
                                                        ->extraAttributes(['data-plus' => '1'])
                                                        ->columnSpan(2),

                                                    // 🎨 выбор цвета
                                                    Forms\Components\Select::make('color_id')
                                                        ->label('Цвет')
                                                        ->relationship('color', 'name')
                                                        ->searchable()
                                                        ->preload()
                                                        ->required()
                                                        ->columnSpan(4),


                                                ]),
                                        ]),
                                ])
                                ->columnSpanFull()
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
