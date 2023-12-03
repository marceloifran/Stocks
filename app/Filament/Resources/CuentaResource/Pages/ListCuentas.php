<?php

namespace App\Filament\Resources\CuentaResource\Pages;

use App\Filament\Resources\CuentaResource;
use App\Filament\Resources\CuentaResource\RelationManagers\TransaccionesRelationManager;
use App\Models\cuenta;
use App\Models\transacciones;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListCuentas extends ListRecords
{
    protected static string $resource = CuentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Cuenta'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todas' => Tab::make()
                ->icon('heroicon-o-inbox-stack')
                ->badge(cuenta::all()->count()),
            'Activo' => Tab::make()
                ->icon('heroicon-o-arrow-up')
                ->badge(cuenta::where('tipo', 'Activo')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo','Activo')),
            'Pasivo' => Tab::make()
                ->icon('heroicon-o-arrow-down')
                ->badge(cuenta::where('tipo', 'Pasivo')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tipo', 'Pasivo')),
        ];
    }
}
