<?php

namespace App\Filament\Resources;

use App\Enums\GenderEnum;
use App\Enums\SortbyEnum;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): string
    {
        return __("category");
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation:category');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ru')->label(__('name'))
                    ->required(),
                Forms\Components\TextInput::make('position')->label(__('position'))
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('gender')->label(__('gender'))
                    ->options(GenderEnum::toObject())
                    ->required(),
                Forms\Components\Select::make('sortby')->label(__('sortby'))
                    ->options([
                        SortbyEnum::CREATED_AT=>"Время создания",
                        SortbyEnum::PRICE => "Цена",
                        SortbyEnum::DISCOUNT => "Скидка"
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('image')->label(__('image'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('name')),
                Tables\Columns\TextColumn::make('position')->label(__('position')),
                Tables\Columns\TextColumn::make('gender')->label(__('gender')),
                Tables\Columns\TextColumn::make('sortby')->label(__('sortby')),
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
            ]);
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
