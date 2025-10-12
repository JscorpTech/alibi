<?php

namespace App\Filament\Resources\OfflineClientResource\Pages;

use App\Filament\Resources\OfflineClientResource;
use Filament\Resources\Pages\ListRecords;

class ListOfflineClients extends ListRecords
{
    protected static string $resource = OfflineClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()->label('Новый офлайн-клиент'),
        ];
    }
}