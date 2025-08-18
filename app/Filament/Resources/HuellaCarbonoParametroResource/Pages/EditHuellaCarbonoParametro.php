<?php

namespace App\Filament\Resources\HuellaCarbonoParametroResource\Pages;

use App\Filament\Resources\HuellaCarbonoParametroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHuellaCarbonoParametro extends EditRecord
{
    protected static string $resource = HuellaCarbonoParametroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
