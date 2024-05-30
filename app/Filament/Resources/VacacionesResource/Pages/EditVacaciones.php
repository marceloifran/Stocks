<?php

namespace App\Filament\Resources\VacacionesResource\Pages;

use App\Filament\Resources\VacacionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVacaciones extends EditRecord
{
    protected static string $resource = VacacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
