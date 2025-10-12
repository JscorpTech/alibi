<?php

namespace App\Filament\Resources\OfflineClientResource\Pages;

use App\Filament\Resources\OfflineClientResource;
use Filament\Resources\Pages\EditRecord;

class EditOfflineClient extends EditRecord
{
    protected static string $resource = OfflineClientResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Изменения сохранены';
    }
}