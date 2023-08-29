<?php

namespace App\Filament\Resources\EquiposResource\Pages;

use App\Filament\Resources\EquiposResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEquipos extends EditRecord
{
    protected static string $resource = EquiposResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
