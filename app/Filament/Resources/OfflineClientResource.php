<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfflineClientResource\Pages;
use App\Models\OfflineClient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfflineClientResource extends Resource
{
    protected static ?string $model = OfflineClient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Продажи';
    protected static ?string $navigationLabel = 'Офлайн-клиенты';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Данные клиента')
                ->schema([
                    Forms\Components\TextInput::make('full_name')
                        ->label('Имя и фамилия')
                        ->maxLength(150)
                        ->required(),

                    Forms\Components\TextInput::make('phone')
                        ->label('Телефон')
                        ->placeholder('+998 90 123 45 67')
                        ->tel()
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(150)
                        ->nullable(),

                    Forms\Components\TextInput::make('discount_percent')
                        ->label('Скидка (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(1)
                        ->helperText('0–100'),

                    Forms\Components\Textarea::make('notes')
                        ->label('Заметки')
                        ->rows(3)
                        ->maxLength(2000)
                        ->nullable(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('ФИО')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percent')->label('Скидка (%)')
                    ->label('Скидка %')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('order_groups_count')
                    ->label('Чеков')
                    ->counts('orderGroups')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Офлайн-клиентов пока нет')
            ->emptyStateDescription('Создайте клиента для работы кассы POS без приложения.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Новый офлайн-клиент'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfflineClients::route('/'),
            'create' => Pages\CreateOfflineClient::route('/create'),
            'edit' => Pages\EditOfflineClient::route('/{record}/edit'),
            'view' => Pages\ViewOfflineClient::route('/{record}'),
        ];
    }
}