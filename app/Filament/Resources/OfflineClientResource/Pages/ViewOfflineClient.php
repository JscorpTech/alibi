<?php

namespace App\Filament\Resources\OfflineClientResource\Pages;

use App\Filament\Resources\OfflineClientResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewOfflineClient extends ViewRecord
{
    protected static string $resource = OfflineClientResource::class;

    public function getRelationManagers(): array
    {
        return [];
    }

    public function getHeaderWidgets(): array
    {
        return [];
    }

    // ✅ правильная сигнатура
    // public function getFooter(): ?View
    // {
    //     // Верни Blade-представление или null, если футер не нужен
    //     return view('filament.offline-clients.view-footer', [
    //         'record' => $this->record,
    //     ]);
    // }
}