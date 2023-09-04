<?php

namespace App\Filament\Resources\TransaccionesResource\Pages;

use App\Filament\Resources\TransaccionesResource;
use App\Models\transacciones;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTransacciones extends ListRecords
{
    protected static string $resource = TransaccionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todas' => Tab::make()
                ->icon('heroicon-o-inbox-stack')
                ->badge(transacciones::all()->count()),
            'Ingresos' => Tab::make()
                ->icon('heroicon-o-arrow-up')
                ->badge(transacciones::where('tipo', 'Ingreso')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo','Ingreso')),
            'Egresos' => Tab::make()
                ->icon('heroicon-o-arrow-down')
                ->badge(transacciones::where('tipo', 'Egreso')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo', 'Egreso')),
        ];
    }
}
