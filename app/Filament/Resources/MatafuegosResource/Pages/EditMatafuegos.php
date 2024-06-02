<?php

namespace App\Filament\Resources\MatafuegosResource\Pages;

use App\Filament\Resources\MatafuegosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMatafuegos extends EditRecord
{
    protected static string $resource = MatafuegosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
