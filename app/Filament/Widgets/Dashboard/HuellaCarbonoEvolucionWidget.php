<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\HuellaCarbono;
use App\Models\HuellaCarbonoDetalle;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class HuellaCarbonoEvolucionWidget extends ChartWidget
{
    protected static ?string $heading = 'Evolución de Emisiones';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    // Añadir filtro de periodo
    protected function getFilters(): ?array
    {
        return [
            'semana' => 'Últimos 7 días',
            'mes' => 'Últimos 30 días',
            'trimestre' => 'Últimos 90 días',
        ];
    }

    protected function getData(): array
    {
        // Determinar el rango de fechas según el filtro
        $filter = $this->filter ?? 'mes';

        switch ($filter) {
            case 'semana':
                $startDate = Carbon::now()->subDays(7);
                break;
            case 'trimestre':
                $startDate = Carbon::now()->subDays(90);
                break;
            case 'mes':
            default:
                $startDate = Carbon::now()->subDays(30);
                break;
        }

        $endDate = Carbon::now();

        // Obtener datos de emisiones por tipo
        $emisionesPorTipo = [];

        // Obtener todas las fechas en el rango
        $fechas = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $fechaKey = $currentDate->format('Y-m-d');
            $fechaDisplay = $currentDate->format('d/m');
            $fechas[$fechaKey] = [
                'fecha' => $fechaDisplay,
                'combustible' => 0,
                'electricidad' => 0,
                'residuos' => 0,
                'total' => 0,
            ];
            $currentDate->addDay();
        }

        // Obtener los detalles y agruparlos por fecha
        $detalles = HuellaCarbonoDetalle::with('huellaCarbono')
            ->whereHas('huellaCarbono', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha', [$startDate, $endDate]);
            })
            ->get();

        foreach ($detalles as $detalle) {
            $fechaKey = $detalle->huellaCarbono->fecha->format('Y-m-d');
            $categoria = $detalle->detalles['categoria'] ?? 'otros';

            if (isset($fechas[$fechaKey])) {
                if ($categoria === 'combustible') {
                    $fechas[$fechaKey]['combustible'] += $detalle->emisiones_co2;
                } elseif ($categoria === 'electricidad') {
                    $fechas[$fechaKey]['electricidad'] += $detalle->emisiones_co2;
                } elseif ($categoria === 'residuos') {
                    $fechas[$fechaKey]['residuos'] += $detalle->emisiones_co2;
                }

                $fechas[$fechaKey]['total'] += $detalle->emisiones_co2;
            }
        }

        // Convertir a array de valores
        $emisionesPorTipo = array_values($fechas);

        // Calcular la tendencia
        $totalEmisiones = array_sum(array_column($emisionesPorTipo, 'total'));
        $mitad = floor(count($emisionesPorTipo) / 2);

        $primeraMitad = 0;
        $segundaMitad = 0;

        for ($i = 0; $i < count($emisionesPorTipo); $i++) {
            if ($i < $mitad) {
                $primeraMitad += $emisionesPorTipo[$i]['total'];
            } else {
                $segundaMitad += $emisionesPorTipo[$i]['total'];
            }
        }

        $tendencia = 'estable';
        $porcentajeCambio = 0;

        if ($primeraMitad > 0) {
            $porcentajeCambio = round((($segundaMitad - $primeraMitad) / $primeraMitad) * 100, 1);
            if ($porcentajeCambio > 5) {
                $tendencia = 'aumento';
            } elseif ($porcentajeCambio < -5) {
                $tendencia = 'disminución';
            }
        }

        // Preparar datos para el gráfico
        $labels = array_column($emisionesPorTipo, 'fecha');
        $dataCombustible = array_column($emisionesPorTipo, 'combustible');
        $dataElectricidad = array_column($emisionesPorTipo, 'electricidad');
        $dataResiduos = array_column($emisionesPorTipo, 'residuos');

        return [
            'datasets' => [
                [
                    'label' => 'Combustible',
                    'data' => $dataCombustible,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Electricidad',
                    'data' => $dataElectricidad,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Residuos',
                    'data' => $dataResiduos,
                    'backgroundColor' => 'rgba(107, 114, 128, 0.5)',
                    'borderColor' => 'rgb(107, 114, 128)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
            'totalEmisiones' => $totalEmisiones,
            'tendencia' => $tendencia,
            'porcentajeCambio' => $porcentajeCambio,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        $data = $this->getData();

        $tendenciaTexto = match ($data['tendencia']) {
            'aumento' => '↑ En aumento',
            'disminución' => '↓ En disminución',
            default => '→ Estable',
        };

        $signo = $data['porcentajeCambio'] > 0 ? '+' : '';

        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => "Total: " . number_format($data['totalEmisiones'], 2) . " kgCO2e · Tendencia: {$tendenciaTexto} ({$signo}{$data['porcentajeCambio']}%)",
                    'position' => 'bottom',
                    'font' => [
                        'size' => 14,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'stacked' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'kgCO2e',
                    ],
                ],
                'x' => [
                    'stacked' => true,
                    'title' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}
