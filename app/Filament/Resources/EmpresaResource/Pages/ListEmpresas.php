<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use App\Models\Empresa;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    protected function getHeaderActions(): array
    {
        return Empresa::count() < 1
            ? [Actions\CreateAction::make()->label('Empresa')->icon('heroicon-o-plus')]
            : []; // 🔥 No muestra el botón si ya hay una empresa
    }
}
