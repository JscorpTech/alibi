<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderDetailResource\Pages\OrderDetail;
use App\Filament\Resources\OrderGroupResource\Pages;
use App\Filament\Resources\OrderGroupResource\RelationManagers;
use App\Models\OrderGroup;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\User;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use App\Models\Variant;


class OrderGroupResource extends Resource
{

    public static function getEloquentQuery(): Builder
    {
        // Без глобальных скоупов и без фильтра по source — видим всё (app + pos)
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
    protected static ?string $model = OrderGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getNavigationLabel(): string
    {
        return __('navigation:orders');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('status')->options([
                        OrderStatusEnum::CANCELED => __("Cancelled"),
                        OrderStatusEnum::PENDING => __("Pending"),
                        OrderStatusEnum::SUCCESS => __("Succeeded"),
                        OrderStatusEnum::DELIVERED => __("Delivered"),
                    ])->required()->label(__('status')),

                    Forms\Components\Select::make('user_id')
                        ->label(__('user_id'))
                        ->searchable()
                        ->preload()
                        // как строить список результатов
                        ->getSearchResultsUsing(function (string $search): array {
                            return User::query()
                                ->select('id', 'full_name', 'phone', 'email')
                                ->when($search, fn($q) => $q->where(function ($qq) use ($search) {
                                    $qq->where('phone', 'like', "%{$search}%")
                                        ->orWhere('full_name', 'like', "%{$search}%")
                                        ->orWhere('email', 'like', "%{$search}%");
                                }))
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(function ($u) {
                                    $label = trim((string) ($u->phone ?? $u->full_name ?? $u->email ?? ''));
                                    if ($label === '')
                                        $label = "User #{$u->id}";
                                    return [$u->id => $label];
                                })
                                ->all();
                        })
                        // как показывать выбранное значение
                        ->getOptionLabelUsing(function ($value): ?string {
                            if (!$value)
                                return null;
                            $u = User::query()->select('id', 'full_name', 'phone', 'email')->find($value);
                            if (!$u)
                                return "User #{$value}";
                            $label = trim((string) ($u->phone ?? $u->full_name ?? $u->email ?? ''));
                            return $label !== '' ? $label : "User #{$u->id}";
                        })
                        ->required(),
                    Forms\Components\TextInput::make('cashback')->label(__('cashback')),
                    Forms\Components\TextInput::make('given_cashback')->label(__('given_cashback')),
                ])->columns(2),
                Forms\Components\Repeater::make('orders')
                    ->relationship('orders')
                    ->label(__('orders'))
                    ->schema([
                        // 1) Товар
                        Forms\Components\Select::make('product_id')
                            ->label(__('product'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(function (string $search): array {
                                return Product::query()
                                    ->select('id', 'name_ru')
                                    ->when($search, fn($q) => $q->where('name_ru', 'ilike', "%{$search}%")) // Postgres
                                    ->orderByDesc('id')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($p) {
                                        $label = trim((string) ($p->name_ru ?? ''));
                                        if ($label === '')
                                            $label = "Product #{$p->id}";
                                        return [$p->id => $label];
                                    })
                                    ->all();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                if (!$value)
                                    return null;
                                $p = Product::query()->select('id', 'name_ru')->find($value);
                                if (!$p)
                                    return "Product #{$value}";
                                $label = trim((string) ($p->name_ru ?? ''));
                                return $label !== '' ? $label : "Product #{$p->id}";
                            })
                            ->reactive(),

                        // 2) Вариант (зависит от выбранного товара)
                        Forms\Components\Select::make('variant_id')
                            ->label('Variant')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(function (Get $get): array {
                                $productId = (int) ($get('product_id') ?? 0);
                                if (!$productId)
                                    return [];
                                return Variant::query()
                                    ->where('product_id', $productId)
                                    ->orderBy('id')
                                    ->get(['id', 'attrs', 'sku', 'stock', 'price'])
                                    ->mapWithKeys(function (Variant $v) {
                                        $attrs = collect((array) ($v->attrs ?? []))
                                            ->map(fn($val, $key) => "{$key}: {$val}")
                                            ->implode(' / ');
                                        $left = $attrs !== '' ? $attrs : "Variant #{$v->id}";
                                        $sku = $v->sku ? " • SKU: {$v->sku}" : '';
                                        $stk = " • stock: " . (int) $v->stock;
                                        $price = (int) ($v->price ?? 0);
                                        $pt = $price > 0 ? (' • ' . number_format($price, 0, '.', ' ')) : '';
                                        $label = $left . $sku . $stk . $pt; // всегда строка
                                        return [$v->id => $label];
                                    })
                                    ->all();
                            })
                            ->disabled(fn(Get $get): bool => empty($get('product_id')))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                $variant = $state ? Variant::find($state) : null;
                                if ($variant && (int) $variant->price > 0) {
                                    $set('price', (int) $variant->price);
                                } else {
                                    $p = ($pid = $get('product_id')) ? Product::find($pid) : null;
                                    if ($p)
                                        $set('price', (int) $p->price);
                                }
                            }),

                        Forms\Components\Placeholder::make('variant_photo')
                            ->label('Фото варианта')
                            ->content(
                                fn($record) => $record && $record->variant_image_url
                                ? new HtmlString('<img src="' . $record->variant_image_url . '" class="h-20 rounded-xl" />')
                                : new HtmlString('<div class="text-gray-400">нет фото</div>')
                            )
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        // 3) Цена/скидка/кол-во
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->label(__('price'))
                            ->required(),

                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->label(__('discount'))
                            ->default(0),

                        Forms\Components\TextInput::make('count')
                            ->numeric()
                            ->label(__('count'))
                            ->required()
                            ->default(1),
                    ])
                    ->grid(1),
                Forms\Components\Section::make('Address')->schema([

                    Forms\Components\Group::make()
                        ->relationship('address')
                        ->label(__('address'))
                        ->schema([
                            Forms\Components\TextInput::make('label')->label(__('label')),
                            Forms\Components\Select::make('region_id')
                                ->relationship('region', 'name_ru')
                                ->searchable()
                                ->getOptionLabelFromRecordUsing(function ($record): string {
                                    if ($record && $record->name_ru) {
                                        return (string) $record->name_ru;
                                    }
                                    // если по какой-то причине $record пустой
                                    return $record && $record->id ? 'Region #' . $record->id : '—';
                                }),

                            Forms\Components\Select::make('district_id')
                                ->relationship('district', 'name_ru')
                                ->searchable()
                                ->getOptionLabelFromRecordUsing(function ($record): string {
                                    if ($record && $record->name_ru) {
                                        return (string) $record->name_ru;
                                    }
                                    return $record && $record->id ? 'District #' . $record->id : '—';
                                }),
                            Actions::make([
                                Action::make(__('Show Address'))
                                    ->icon('heroicon-m-building-office')
                                    ->color('info')
                                    ->action(function ($record) {
                                        $lon = $record->address?->long;
                                        $lat = $record->address?->lat;
                                        if ($lon && $lat) {
                                            return redirect("https://yandex.ru/maps/?pt={$lon},{$lat}&z=18&l=map");
                                        }
                                    }),
                            ]),
                        ])->columns(2),
                    Forms\Components\DatePicker::make('delivery_date')->label(__('delivery_date')),
                ]),

            ]);
    }

    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('id')),
                Tables\Columns\TextColumn::make('user.phone')->label(__('user')),
                Tables\Columns\TextColumn::make('address.label')->label(__('address')),
                Tables\Columns\TextColumn::make('status')->label(__('status'))->state(function (OrderGroup $record) {
                    return __($record->status);
                }),
                Tables\Columns\BadgeColumn::make('source')
                    ->label('Источник')
                    ->colors([
                        'primary' => fn($state) => $state === 'app',
                        'success' => fn($state) => $state === 'pos',
                    ])
                    ->formatStateUsing(fn(?string $state) => $state === 'pos' ? 'POS касса' : 'Приложение'),

                Tables\Columns\TextColumn::make('cashback')->label(__('cashback')),
                Tables\Columns\TextColumn::make('given_cashback')->label(__('given_cashback')),
                Tables\Columns\TextColumn::make('variant.attrs')
                    ->label('Variant')
                    ->formatStateUsing(fn($state) => collect((array) $state)->map(fn($v, $k) => "$k: $v")->implode(' / ')),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Items')

            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(\App\Enums\OrderStatusEnum::toObject())
                    ->label(__('status')),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Источник')
                    ->options([
                        'app' => 'Приложение',
                        'pos' => 'POS касса',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOrderGroups::route('/'),
            'create' => Pages\CreateOrderGroup::route('/create'),
            'edit' => Pages\EditOrderGroup::route('/{record}/edit'),
            'view' => Pages\EditOrderGroup::route('/{record}/view'),
        ];
    }
}
