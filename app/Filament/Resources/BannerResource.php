<?php

namespace App\Filament\Resources;

use App\Enums\BannerEnum;
use App\Enums\BannerStatusEnum;
use App\Enums\BannerTypeEnum;
use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getNavigationLabel(): string
    {
        return __('navigation:banner');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('title')->required()->label(__('title')),
                    Forms\Components\TextInput::make('subtitle')->required()->label(__('subtitle')),
                ])->columns(2),
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            BannerStatusEnum::ACTIVE    => __('active'),
                            BannerStatusEnum::NOTACTIVE => __('not-active'),
                        ])
                        ->required()->label(__('status')),
                    Forms\Components\Select::make('position')->required()->options([
                        BannerEnum::TOP    => __('top'),
                        BannerEnum::BOTTOM => __('bottom'),
                    ])->label(__('position')),
                ])->columns(2),
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('link')->required()->label(__('link')),
                    Forms\Components\TextInput::make('link_text')->required()->label(__('link_text')),
                ])->columns(2),
                Forms\Components\Section::make()->schema([
                    Forms\Components\FileUpload::make('image')->required()->label(__('image')),
                    Forms\Components\Select::make('type')->label(__('type'))
                        ->options([
                            BannerTypeEnum::WEB    => 'Web Site',
                            BannerTypeEnum::MOBILE => 'Mobile',
                        ])->required(),
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('subtitle'),
                Tables\Columns\TextColumn::make('link'),
                Tables\Columns\TextColumn::make('type'),
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
            'index'  => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit'   => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
