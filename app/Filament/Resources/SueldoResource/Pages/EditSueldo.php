<?php

namespace App\Filament\Resources\SueldoResource\Pages;

use App\Filament\Resources\SueldoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSueldo extends EditRecord
{
    protected static string $resource = SueldoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
