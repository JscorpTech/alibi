<?php

namespace App\Filament\Resources\StockLocationResource\RelationManagers;

use App\Models\InventoryLevel;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'inventoryLevels'; // связь на модели StockLocation

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('product.name_ru')
                    ->label('Товар')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('qty_on_hand')
                    ->label('В наличии')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_reserved')
                    ->label('Резерв')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Обновлён'),
            ])
            ->filters([])
            // обычно правят через приход/продажу, поэтому делаем read-only
            ->actions([]) 
            ->bulkActions([]);
    }
}