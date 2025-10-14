<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\POS\{
    CartTrait,
    SearchTrait,
    CustomerTrait,
    ReturnTrait,
    CheckoutTrait,
    HelpersTrait,
    ReceiptTrait  // ✅ DOBAV' ETO
};

class Pos extends Page
{
    use CartTrait,
        SearchTrait,
        CustomerTrait,
        ReturnTrait,
        CheckoutTrait,
        HelpersTrait,
        ReceiptTrait;  // ✅ I ETO

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Касса (POS)';
    protected static string $view = 'filament.pages.pos';

    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = -100000;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}