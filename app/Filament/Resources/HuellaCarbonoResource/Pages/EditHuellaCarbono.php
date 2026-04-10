<?php

namespace App\Filament\Resources\HuellaCarbonoResource\Pages;

use App\Filament\Resources\HuellaCarbonoResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditHuellaCarbono extends EditRecord
{
    protected static string $resource = HuellaCarbonoResource::class;

    // Redirigir a la lista después de editar
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Desactivar la notificación automática de Filament
    protected function getSavedNotificationTitle(): ?string
    {
        return null;
    }

    // Mostrar nuestra propia notificación
    protected function afterSave(): void
    {
        Notification::make()
            ->title('Registro actualizado')
            ->body('La huella de carbono se ha actualizado correctamente.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Si hay detalles, tomar el primero para mostrar en el formulario
        $detalle = $this->record->detalles()->first();

        if ($detalle) {
            $data['categoria_fuente'] = $detalle->detalles['categoria'] ?? null;
            $data['tipo_fuente'] = $detalle->tipo_fuente;
            $data['identificador_fuente'] = $detalle->detalles['identificador_fuente'] ?? '';
            $data['horas_operacion'] = $detalle->detalles['horas_operacion'] ?? null;
            $data['cantidad'] = $detalle->cantidad;
            $data['factor_conversion'] = $detalle->factor_conversion;
            $data['unidad'] = $detalle->unidad;
            $data['emisiones_co2'] = $detalle->emisiones_co2;
        }

        return $data;
    }
}
