<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make("product_id")
                    ->relationship('product', 'name_ru')
                    ->searchable()
                    ->preload()
                    ->label(__('product')),

                    Forms\Components\FileUpload::make("path")
                    ->label("Видео")
                    ->directory('videos') // кладёт в storage/app/public/videos
                    ->disk('public')      // Laravel будет отдавать через /storage/videos
                    ->visibility('public')
                    ->required(),

                Forms\Components\Hidden::make('status')
                    ->default('uploaded'),
            ])
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make("product.name")->label("Продукт"),
            TextColumn::make("product.price")->label("Цена"),
            TextColumn::make("product.discount")->label("Скидка"),

            TextColumn::make("status")
                ->label("Статус")
                ->badge()
                ->colors([
                    'primary' => 'uploaded',
                    'success' => 'ready',
                    'danger' => 'failed',
                ]),

            // 🖼 Превью картинки
            TextColumn::make("thumbnail_path")
            ->label("Превью")
            ->html()
            ->formatStateUsing(function (?string $state) {
                if (!$state) return '-';
                return "<img src='/storage/{$state}' width='80' />";
            }),

         
          
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}