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
        $emisionesPorTipo = HuellaCarbonoDetalle::with('huellaCarbono')
            ->whereHas('huellaCarbono', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy(function ($detalle) {
                return $detalle->huellaCarbono->fecha->format('Y-m-d');
            })
            ->map(function ($grupo) {
                $fecha = $grupo->first()->huellaCarbono->fecha->format('d/m');

                // Agrupar por categoría
                $porCategoria = $grupo->groupBy(function ($detalle) {
                    return $detalle->detalles['categoria'] ?? 'otros';
                });

                return [
                    'fecha' => $fecha,
                    'combustible' => $porCategoria->get('combustible', collect())->sum('emisiones_co2'),
                    'electricidad' => $porCategoria->get('electricidad', collect())->sum('emisiones_co2'),
                    'residuos' => $porCategoria->get('residuos', collect())->sum('emisiones_co2'),
                    'total' => $grupo->sum('emisiones_co2'),
                ];
            })
            ->sortBy(function ($item, $key) {
                return Carbon::createFromFormat('d/m', $item['fecha'])->format('Y-m-d');
            })
            ->values();

        // Calcular la tendencia (si aumenta o disminuye)
        $totalEmisiones = $emisionesPorTipo->sum('total');
        $primeraMitad = $emisionesPorTipo->take(ceil($emisionesPorTipo->count() / 2))->sum('total');
        $segundaMitad = $emisionesPorTipo->skip(floor($emisionesPorTipo->count() / 2))->sum('total');

        $tendencia = 'estable';
        $porcentajeCambio = 0;

        if ($emisionesPorTipo->count() > 1 && $primeraMitad > 0) {
            $porcentajeCambio = round((($segundaMitad - $primeraMitad) / $primeraMitad) * 100, 1);
            if ($porcentajeCambio > 5) {
                $tendencia = 'aumento';
            } elseif ($porcentajeCambio < -5) {
                $tendencia = 'disminución';
            }
        }

        // Preparar datos para el gráfico
        $labels = $emisionesPorTipo->pluck('fecha')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Combustible',
                    'data' => $emisionesPorTipo->pluck('combustible')->toArray(),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Electricidad',
                    'data' => $emisionesPorTipo->pluck('electricidad')->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Residuos',
                    'data' => $emisionesPorTipo->pluck('residuos')->toArray(),
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
        return 'line';
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
