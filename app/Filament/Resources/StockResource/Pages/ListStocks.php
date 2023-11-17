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
            Actions\CreateAction::make()->label('Nuevo Stock')->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make()
                ->icon('heroicon-o-inbox-stack')
                ->badge(stock::all()->count()),
            'Stock Bajo' => Tab::make()
                ->icon('heroicon-o-arrow-down')
                ->badge(stock::where('cantidad', '<=', 10)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('cantidad', '<=', 10)),
            'Stock Alto' => Tab::make()
                ->icon('heroicon-o-arrow-up')
                ->badge(stock::where('cantidad', '>=', 10)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('cantidad', '>=', 10)),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            //  StockOverview::class,
            // StockChart::class
        ];
    }
}
