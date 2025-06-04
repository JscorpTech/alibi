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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Maxsulotlar';

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
        return (new Product())->newQueryWithoutScopes();
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation:products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name_ru')->label(__('name')),
                    Forms\Components\TextInput::make('label')->label(__("label")),
                    Forms\Components\RichEditor::make('desc_ru')->label(__('desc')),
                ])->columns(1),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Toggle::make('is_active')->label(__("is:active")),
                    Forms\Components\Select::make('categories')
                        ->relationship('categories', 'name_ru')
                        ->required()
                        ->multiple()
                        ->preload()
                        ->label(__('category')),
                    Forms\Components\Select::make('tags')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->preload()
                        ->label(__('tags')),
                    Forms\Components\Select::make('brand')
                        ->relationship('brand', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('brand')),
                    Forms\Components\Select::make('subcategories')
                        ->relationship('subcategories', 'name_ru')
                        ->multiple()
                        ->preload()
                        ->label(__('subcategory')),
                    Forms\Components\Select::make('offers')
                        ->options(function () {
                            $subcategories = [];
                            SubCategory::query()->each(function ($item) use (&$subcategories) {
                                $subcategories[$item->id] = $item->name_ru;
                            });

                            return $subcategories;
                        })
                        ->multiple()
                        ->label(__('offers')),
                ])->columns(4),
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->required()
                        ->label(__('price')),
                    Forms\Components\TextInput::make('discount')
                        ->numeric()
                        ->default(0)
                        ->label(__('discount')),
                    Forms\Components\Select::make('sizes')
                        ->relationship('sizes', 'name')
                        ->multiple()
                        ->preload()
                        ->required()
                        ->label(__('sizes')),
                ])->columns(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('gender')
                        ->options([
                            GenderEnum::MALE   => __('MALE'),
                            GenderEnum::FEMALE => __('FEMALE'),
                        ])
                        ->required()
                        ->label(__('gender')),
                    Forms\Components\Select::make('status')
                        ->options([
                            ProductStatusEnum::AVAILABLE     => __('AVAILABLE'),
                            ProductStatusEnum::EXPECTED      => __('EXPECTED'),
                            ProductStatusEnum::NOT_AVAILABLE => __('NOT_AVAILABLE'),
                        ])
                        ->required()
                        ->label(__('status')),
                    Forms\Components\TextInput::make('sku'),
                ])->columns(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Repeater::make('product_options')
                        ->relationship('product_options')
                        ->schema([
                            Forms\Components\TextInput::make('name')->label(__("Описание продукта"))
                                ->required(),
                            Forms\Components\Repeater::make('items')->relationship("items")->schema([
                                Forms\Components\TextInput::make('name')->label(__("заголовок")),
                                Forms\Components\TextInput::make('desc')->label(__("Описание о заголовке")),
                            ])->label(__("заголовок"))
                        ])->grid([
                            'lg' => 4,
                            'md' => 3,
                            'sm' => 2,
                        ])->label(__('Описание продукта')),
                ]),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Repeater::make('options')
                        ->relationship('options')
                        ->schema([
                            Forms\Components\Select::make('color_id')
                                ->relationship('color', 'name')
                                ->required()
                                ->label(__('color')),
                            Forms\Components\Select::make('color_product_id')
                                ->relationship('color_product', 'name_ru')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label(__('product')),
                            Forms\Components\Select::make('size_id')
                                ->relationship('size', 'name')
                                ->required()
                                ->label(__('size')),
                            Forms\Components\TextInput::make('count')
                                ->numeric()
                                ->required()
                                ->label(__('count')),
                        ])->grid([
                            'lg' => 4,
                            'md' => 3,
                            'sm' => 2,
                        ])->label(__('options')),
                ]),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Repeater::make('colors')
                        ->relationship('colors')
                        ->schema([
                            Forms\Components\Select::make('color_id')
                                ->relationship('color', 'name')
                                ->label(__('color')),
                            Forms\Components\Repeater::make('image')
                                ->relationship('image')
                                ->schema([
                                    Forms\Components\FileUpload::make('path'),
                                ])
                                ->maxItems(1)
                                ->minItems(1)
                                ->label(__('image')),
                        ])->grid(3)->label(__('colors')),
                ]),
                Forms\Components\Section::make()->schema([
                    Forms\Components\FileUpload::make('image')
                        ->required()
                        ->label(__('image')),
                ]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->relationship('images')
                            ->schema([
                                Forms\Components\FileUpload::make('path'),
                            ])
                            ->grid(3)
                            ->label(__('images')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label(__('id')),
                Tables\Columns\ImageColumn::make('image')
                    ->width(50)
                    ->height(50),
                Tables\Columns\TextColumn::make('name_ru')
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('label')
                    ->label(__('label')),
                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->label(__('price')),
                Tables\Columns\TextColumn::make('discount')
                    ->sortable()
                    ->label(__('discount')),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->label(__('created_at')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
