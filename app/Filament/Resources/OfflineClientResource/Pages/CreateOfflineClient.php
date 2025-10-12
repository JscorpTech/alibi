<?php

namespace App\Filament\Resources\OfflineClientResource\Pages;

use App\Filament\Resources\OfflineClientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOfflineClient extends CreateRecord
{
    protected static string $resource = OfflineClientResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Клиент создан';
    }
}