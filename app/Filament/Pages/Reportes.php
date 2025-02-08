<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use App\Filament\Resources\StockResource\Widgets\StockChart;
use App\Filament\Resources\StockResource\Widgets\StockOverview;
use App\Filament\Resources\StockMovementResource\Widgets\MovementOverview;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementChart;


class Reportes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Reportes del Sistema';

    protected static string $view = 'filament.pages.reportes';

    protected function getHeaderWidgets(): array
    {
        return [
            StockMovementChart::class,
            MovementOverview::class,
             StockOverview::class,
             StockChart::class
        ];
    }
}
