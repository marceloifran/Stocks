<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\StockMovement;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\StockMovementResource;
use App\Filament\Resources\StockMovementResource\Widgets\MovementOverview;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementChart;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementsChart;


class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(trans('actions.movementb'))->icon('heroicon-o-plus'),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            //  MovementOverview::class,
            //  StockMovementChart::class
        ];
    }
}
