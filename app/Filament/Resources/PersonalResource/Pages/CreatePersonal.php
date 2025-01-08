<?php

namespace App\Filament\Resources\PersonalResource\Pages;

use App\Filament\Resources\PersonalResource;
use App\Models\personal;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonal extends CreateRecord
{
    protected static string $resource = PersonalResource::class;

    protected function getRedirectUrl(): string
    {
        $nombrepersona = personal::find($this->record->id)->nombre;
        Notification::make()
        ->title('Nuevo persona en '.$nombrepersona)
        ->body('Se ha agregado una nueva persona')
        ->success();
        return $this->getResource()::getUrl('index');
    }
}
