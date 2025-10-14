<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProductSalesReport extends Page
{
    protected static ?string $navigationGroup = 'Products';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Sales report';
    protected static string $view = 'filament.pages.sales-dashboard';
}