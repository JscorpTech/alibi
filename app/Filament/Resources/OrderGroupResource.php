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
                        ->relationship('user', 'phone')
                        ->searchable()
                        ->preload()
                        ->label(__('user_id')),
                    Forms\Components\TextInput::make('cashback')->label(__('cashback')),
                    Forms\Components\TextInput::make('given_cashback')->label(__('given_cashback')),
                ])->columns(2),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Repeater::make('orders')
                        ->relationship('orders')
                        ->label(__('orders'))
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->relationship('product', 'name_ru')
                                ->searchable()
                                ->label(__('product'))
                                ->preload(),
                            Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->label(__('price'))
                                ->required(),
                            Forms\Components\TextInput::make('discount')
                                ->numeric()
                                ->label(__('discount'))
                                ->required(),
                            Forms\Components\TextInput::make('count')
                                ->numeric()
                                ->label(__('count'))
                                ->required(),
                            Forms\Components\Select::make('color_id')
                                ->relationship('color', 'name')->label(__('color')),
                            Forms\Components\Select::make('size_id')
                                ->relationship('size', 'name')->label(__('size')),
                            Actions::make([
                                Action::make(__('Show Product'))
                                    ->icon('heroicon-s-link')
                                    ->color('info')
                                    ->action(function ($record) {
                                        return redirect(route('show', ['id' => $record->product->id]));
                                    }),
                            ]),
                        ])->grid(3),
                ]),
                Forms\Components\Section::make('Address')->schema([

                    Forms\Components\Group::make()
                        ->relationship('address')
                        ->label(__('address'))
                        ->schema([
                            Forms\Components\TextInput::make('label')->label(__('label')),
                            Forms\Components\Select::make('region_id')->label(__('region'))
                                ->relationship('region', 'name_ru'),
                            Forms\Components\Select::make('district_id')->label(__('district'))
                                ->relationship('district', 'name_ru'),
                            Actions::make([
                                Action::make(__('Show Address'))
                                    ->icon('heroicon-m-building-office')
                                    ->color('info')
                                    ->action(function ($record) {
                                        return redirect("https://yandex.ru/maps/?pt={{$record->address?->long}},{{$record->address?->lat}}&z=18&l=map");
                                    }),
                            ]),
                        ])->columns(3),
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
