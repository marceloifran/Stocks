<?php

namespace App\Filament\Resources\HuellaCarbonoResource\Pages;

use App\Filament\Resources\HuellaCarbonoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;

class ListHuellaCarbonos extends ListRecords
{
    protected static string $resource = HuellaCarbonoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('+ Huella'),

            Actions\Action::make('export_pdf')
                ->label('Exportar Reporte')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('periodo')
                        ->label('Periodo')
                        ->options([
                            'semana' => 'Últimos 7 días',
                            'quincena' => 'Últimos 15 días',
                            'mes' => 'Últimos 30 días',
                            'todo' => 'Histórico completo',
                        ])
                        ->default('mes')
                        ->required(),
                ])
                ->url(function (array $data) {
                    // Verificar si existe el periodo y usar un valor predeterminado si no
                    $periodo = $data['periodo'] ?? 'mes';

                    // Obtener datos para el reporte
                    $huellasCarbono = HuellaCarbono::with('detalles')->get();
                    $totalEmisiones = $huellasCarbono->sum('total_emisiones');

                    // Calcular estadísticas por tipo de fuente
                    $estadisticas = [
                        'combustible' => 0,
                        'electricidad' => 0,
                        'residuos' => 0,
                    ];

                    foreach ($huellasCarbono as $huella) {
                        foreach ($huella->detalles as $detalle) {
                            if (isset($detalle->detalles['categoria'])) {
                                $categoria = $detalle->detalles['categoria'];
                                if (isset($estadisticas[$categoria])) {
                                    $estadisticas[$categoria] += $detalle->emisiones_co2;
                                }
                            }
                        }
                    }

                    // Generar URL para el reporte
                    return route('huella-carbono.report', [
                        'totalEmisiones' => $totalEmisiones,
                        'estadisticas' => json_encode($estadisticas),
                        'periodo' => $periodo,
                    ]);
                })
                ->openUrlInNewTab(),
        ];
    }
}
