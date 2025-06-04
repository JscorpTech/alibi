<?php

namespace App\Filament\Resources;

use App\Enums\PageCategoryEnum;
use App\Filament\Resources\PagesResource\Pages;
use App\Filament\Resources\PagesResource\RelationManagers;
use App\Models\Pages as PagesModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PagesResource extends Resource
{
    protected static ?string $model = PagesModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    public static function getNavigationLabel(): string
    {
        return __('navigation:pages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')->required()->label(__('title')),
                                Forms\Components\TextInput::make('path')->required()->label(__('path')),
                            ])->columns(),
                        Forms\Components\Group::make()
                            ->schema([

                                Forms\Components\Select::make('category')
                                    ->options([
                                        PageCategoryEnum::COMPANY => 'Kompaniya',
                                        PageCategoryEnum::TERMS   => 'SHARTLAR VA MAXFIYLIK',
                                    ])->native(false)->label(__('category')),
                                Forms\Components\FileUpload::make('image')->required(),
                            ])->columns(),
                        Forms\Components\RichEditor::make('content')->required()->label(__('content')),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label(__('image')),
                Tables\Columns\TextColumn::make('title')->label(__('title')),
                Tables\Columns\TextColumn::make('path')->label(__('path')),
                Tables\Columns\TextColumn::make('category')->label(__('category')),
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
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePages::route('/create'),
            'edit'   => Pages\EditPages::route('/{record}/edit'),
        ];
    }
}
