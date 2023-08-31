<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use App\Filament\Resources\StockMovementResource\Widgets\MovementOverview;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementsChart;
use App\Livewire\StockMovementChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo Movimiento')->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // MovementOverview::class,
            StockMovementsChart::class
        ];
    }
}
