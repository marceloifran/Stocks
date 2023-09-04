<?php

namespace App\Filament\Resources\TransaccionesResource\Pages;

use App\Filament\Resources\TransaccionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransacciones extends EditRecord
{
    protected static string $resource = TransaccionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
