<?php

namespace App\Filament\Resources\EquiposResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\equipos;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EquiposResource;

class CreateEquipos extends CreateRecord
{
    protected static string $resource = EquiposResource::class;

    
    protected function getRedirectUrl(): string
    {
        $equipo = equipos::find($this->record->permiso_id);
        Notification::make()
        ->title('Nuevo Equipo')
        ->body('Se ha creado un nuevo equipo')
        ->success()
        ->sendToDatabase(User::whereNotNull('email_verified_at')->get());
        return $this->getResource()::getUrl('index');
        

    }
}
