<?php

namespace App\Filament\Resources;

use App\Enums\GenderEnum;
use App\Filament\Resources\SubCategoryResource\Pages;
use App\Filament\Resources\SubCategoryResource\RelationManagers;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function getNavigationGroup(): string
    {
        return __("category");
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation:subcategory');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_ru')
                    ->required()
                    ->label(__('name')),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->label(__('code')),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name_ru')
                    ->required()
                    ->label(__('category')),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->label(__('position')),
                Forms\Components\Select::make('gender')
                    ->options(GenderEnum::toObject())->required()->label(__('gender')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('name')),
                Tables\Columns\TextColumn::make('category.name')->label(__('category')),
                Tables\Columns\TextColumn::make('position')->label(__('position')),
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
            'index'  => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'edit'   => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
