<?php

namespace App\Filament\Resources\EquiposResource\Pages;

use App\Filament\Resources\EquiposResource;
use App\Filament\Resources\EquiposResource\Widgets\EquiposChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipos extends ListRecords
{
    protected static string $resource = EquiposResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // PersonOverview::class,
            // EquiposChart::class
        ];
    }
}
