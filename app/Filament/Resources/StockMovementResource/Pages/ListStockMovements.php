<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use Filament\Actions;
use App\Models\StockMovement;
use App\Livewire\StockMovementChart;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

use App\Filament\Resources\StockMovementResource;
use App\Filament\Resources\StockMovementResource\Widgets\MovementOverview;
use App\Filament\Resources\StockMovementResource\Widgets\StockMovementsChart;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo')->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $month =  Carbon::now()->startOfMonth();

        return [
            'Todos' => Tab::make()
                ->icon('heroicon-o-arrow-path')
                ->badge(StockMovement::all()->count()),
            'Dia' => Tab::make()
            ->icon('heroicon-o-arrow-path')
                ->badge(StockMovement::where('fecha_movimiento',$today )->count())
               ->modifyQueryUsing(fn (Builder $query) => $query->where('fecha_movimiento', $today)),
               'Semana' => Tab::make()
               ->icon('heroicon-o-arrow-path')
               ->badge(StockMovement::whereBetween('fecha_movimiento', [$startOfWeek, $endOfWeek])->count())
               ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('fecha_movimiento', [$startOfWeek, $endOfWeek])),
            'Mes' => Tab::make()
            ->icon('heroicon-o-arrow-path')
            ->badge(StockMovement::whereBetween('fecha_movimiento', [$month, $today])->count())
            ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('fecha_movimiento', [$month, $today])),

        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
             MovementOverview::class,
            StockMovementsChart::class
        ];
    }
}
