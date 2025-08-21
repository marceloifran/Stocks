<?php

namespace App\Filament\Widgets;

use App\Models\HuellaCarbonoDetalle;
use App\Models\HuellaCarbono;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmissionsDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Emisiones';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Obtener el tenant_id del usuario actual
        $tenantId = Auth::user()->tenant_id;

        // Si el usuario tiene tenant, filtrar por tenant_id
        if ($tenantId) {
            // Obtener IDs de las huellas de carbono del tenant
            $huellaIds = HuellaCarbono::where('tenant_id', $tenantId)->pluck('id');

            // Si no hay registros de huella para este tenant, devolver datos vacíos
            if ($huellaIds->isEmpty()) {
                return $this->getEmptyData();
            }

            $query = HuellaCarbonoDetalle::select(
                'tipo_fuente',
                DB::raw('SUM(emisiones_co2) as total')
            )
                ->whereIn('huella_carbono_id', $huellaIds);
        } else if (Auth::user()->hasRole('superadmin')) {
            // Superadmin puede ver todos los datos
            $query = HuellaCarbonoDetalle::select(
                'tipo_fuente',
                DB::raw('SUM(emisiones_co2) as total')
            );
        } else {
            // Usuario sin tenant y que no es superadmin no debería ver datos
            return $this->getEmptyData();
        }

        $data = $query->groupBy('tipo_fuente')
            ->get();

        // Si no hay datos, devolver estructura vacía
        if ($data->isEmpty()) {
            return $this->getEmptyData();
        }

        $categories = [];
        $emissions = [];
        $backgroundColors = [
            'combustible' => 'rgba(246, 153, 63, 0.8)',
            'electricidad' => 'rgba(52, 152, 219, 0.8)',
            'residuos' => 'rgba(46, 204, 113, 0.8)',
        ];

        $borderColors = [
            'combustible' => 'rgb(230, 126, 34)',
            'electricidad' => 'rgb(41, 128, 185)',
            'residuos' => 'rgb(39, 174, 96)',
        ];

        $backgrounds = [];
        $borders = [];

        foreach ($data as $item) {
            $categoryName = ucfirst($item->tipo_fuente);
            $categories[] = $categoryName;
            $emissions[] = round($item->total, 2);

            // Determinar la categoría basada en el tipo_fuente
            $categoria = '';
            if (
                str_contains(strtolower($item->tipo_fuente), 'gasolina') ||
                str_contains(strtolower($item->tipo_fuente), 'diesel') ||
                str_contains(strtolower($item->tipo_fuente), 'combustible')
            ) {
                $categoria = 'combustible';
            } elseif (str_contains(strtolower($item->tipo_fuente), 'electr')) {
                $categoria = 'electricidad';
            } elseif (
                str_contains(strtolower($item->tipo_fuente), 'residu') ||
                str_contains(strtolower($item->tipo_fuente), 'papel') ||
                str_contains(strtolower($item->tipo_fuente), 'organico')
            ) {
                $categoria = 'residuos';
            }

            $backgrounds[] = $backgroundColors[$categoria] ?? 'rgba(149, 165, 166, 0.8)';
            $borders[] = $borderColors[$categoria] ?? 'rgb(127, 140, 141)';
        }

        return [
            'datasets' => [
                [
                    'label' => 'kg CO2e',
                    'data' => $emissions,
                    'backgroundColor' => $backgrounds,
                    'borderColor' => $borders,
                    'borderWidth' => 1,
                ]
            ],
            'labels' => $categories,
        ];
    }

    /**
     * Devuelve una estructura de datos vacía para el gráfico
     */
    protected function getEmptyData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'kg CO2e',
                    'data' => [],
                    'backgroundColor' => [],
                    'borderColor' => [],
                    'borderWidth' => 1,
                ]
            ],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
