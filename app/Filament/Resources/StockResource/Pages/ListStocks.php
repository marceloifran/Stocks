<?php

namespace App\Filament\Resources\StockResource\Pages;

use Filament\Actions;
use App\Filament\Resources\StockResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\StockResource\Widgets\StockChart;
use App\Filament\Resources\StockResource\Widgets\StockOverview;
use Illuminate\Database\Eloquent\Builder;
use App\Models\stock;

class ListStocks extends ListRecords
{
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Stock')->icon('heroicon-o-plus'),
        ];
    }

}
