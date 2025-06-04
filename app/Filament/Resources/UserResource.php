<?php

namespace App\Filament\Resources;

use App\Enums\CardEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('navigation:users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Jscorp Tech')->schema([
                    Forms\Components\TextInput::make('phone')->label(__('phone')),
                    Forms\Components\TextInput::make('full_name')->label(__('full_name')),
                    Forms\Components\Select::make('address_id')
                        ->relationship('address', 'label')
                        ->preload()
                        ->searchable()
                        ->label(__('address')),
                    Forms\Components\Select::make('card')
                        ->options(CardEnum::toObject())
                        ->label(__('card')),
                    Forms\Components\TextInput::make('balance')
                        ->label(__('balance')),
                    Forms\Components\Select::make('is_first_order')->options([
                        true  => 'Ha',
                        false => "Yo'q",
                    ])->label(__('is_first_order')),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone')->label(__('phone')),
                Tables\Columns\TextColumn::make('full_name')->label(__('full_name')),
                Tables\Columns\TextColumn::make('address.label')->label(__('address')),
                Tables\Columns\TextColumn::make('card')->label(__('card')),
                Tables\Columns\TextColumn::make('balance')->label(__('balance')),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
