<?php

namespace App\Filament\Resources\HuellaCarbonoResource\Pages;

use App\Filament\Resources\HuellaCarbonoResource;
use App\Models\HuellaCarbonoDetalle;
use App\Models\HuellaCarbonoParametro;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateHuellaCarbono extends CreateRecord
{
    protected static string $resource = HuellaCarbonoResource::class;

    // Redirigir a la lista después de crear
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Desactivar la notificación automática de Filament
    protected function getCreatedNotificationTitle(): ?string
    {
        return null;
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            // Extraer los datos de la fuente de emisión
            $categoriaFuente = $data['categoria_fuente'] ?? null;
            $tipoFuente = $data['tipo_fuente'] ?? null;
            $identificadorFuente = $data['identificador_fuente'] ?? '';
            $horasOperacion = $data['horas_operacion'] ?? null;
            $cantidad = $data['cantidad'] ?? 0;
            $emisiones = isset($data['emisiones_co2']) ? floatval($data['emisiones_co2']) : 0;

            // Crear el registro principal de huella de carbono
            $huellaCarbono = static::getModel()::create([
                'fecha' => $data['fecha'],
                'notas' => $data['notas'] ?? null,
                'total_emisiones' => $emisiones,
            ]);

            // Si hay datos de fuente de emisión, crear el detalle
            if ($categoriaFuente && $tipoFuente && $cantidad > 0) {
                $parametro = HuellaCarbonoParametro::where('tipo', $tipoFuente)
                    ->where('activo', true)
                    ->first();

                if ($parametro) {
                    HuellaCarbonoDetalle::create([
                        'huella_carbono_id' => $huellaCarbono->id,
                        'tipo_fuente' => $tipoFuente,
                        'cantidad' => $cantidad,
                        'unidad' => $parametro->unidad_medida,
                        'factor_conversion' => $parametro->factor_conversion,
                        'emisiones_co2' => $emisiones,
                        'detalles' => [
                            'categoria' => $categoriaFuente,
                            'identificador_fuente' => $identificadorFuente,
                            'horas_operacion' => $horasOperacion,
                            'observaciones' => '',
                        ],
                    ]);
                }
            }

            DB::commit();

            Notification::make()
                ->title('Registro creado')
                ->body('La huella de carbono se ha registrado correctamente.')
                ->success()
                ->send();

            return $huellaCarbono;
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->body('Ha ocurrido un error al guardar el registro: ' . $e->getMessage())
                ->danger()
                ->send();

            throw $e;
        }
    }
}
