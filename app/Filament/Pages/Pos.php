<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\POS\{
    CartTrait,
    SearchTrait,
    CustomerTrait,
    ReturnTrait,
    CheckoutTrait,
    HelpersTrait
};

class Pos extends Page
{
    use CartTrait, SearchTrait, CustomerTrait, ReturnTrait, CheckoutTrait, HelpersTrait;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Продажи';
    protected static ?string $navigationLabel = 'Касса (POS)';
    protected static string $view = 'filament.pages.pos';
}