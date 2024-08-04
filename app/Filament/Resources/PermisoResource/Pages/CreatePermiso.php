<?php

namespace App\Filament\Resources\PermisoResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\permiso;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PermisoResource;

class CreatePermiso extends CreateRecord
{
    protected static string $resource = PermisoResource::class;



    protected function getRedirectUrl(): string
    {
        $permiso = permiso::find($this->record->permiso_id);
        Notification::make()
        ->title('Nuevo permiso')
        ->body('Se ha creado un nuevo permiso')
        ->icon('heroicon-o-check-circle')
        ->iconColor('success')
        ->success()
        ->sendToDatabase(User::whereNotNull('email_verified_at')->get());
        return $this->getResource()::getUrl('index');

    }
}
