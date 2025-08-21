<?php

namespace App\Filament\Widgets;

use App\Models\HuellaCarbono;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmissionsForecastChart extends ChartWidget
{
    protected static ?string $heading = 'Proyección de Emisiones';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Obtener datos históricos de los últimos 6 meses
        $historicData = $this->getHistoricData();

        // Calcular la proyección para los próximos 3 meses
        $forecast = $this->calculateForecast($historicData);

        // Combinar datos históricos y proyección
        $allMonths = array_merge(array_keys($historicData), array_keys($forecast));
        sort($allMonths);

        $labels = [];
        $historicValues = [];
        $forecastValues = [];

        foreach ($allMonths as $month) {
            $monthName = Carbon::parse($month)->locale('es')->monthName;
            $labels[] = ucfirst($monthName);

            if (isset($historicData[$month])) {
                $historicValues[] = $historicData[$month];
                $forecastValues[] = null;
            } else {
                $historicValues[] = null;
                $forecastValues[] = $forecast[$month];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Emisiones históricas',
                    'data' => $historicValues,
                    'fill' => false,
                    'borderColor' => 'rgb(72, 187, 120)',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Proyección',
                    'data' => $forecastValues,
                    'fill' => false,
                    'borderColor' => 'rgb(237, 137, 54)',
                    'borderDash' => [5, 5],
                    'tension' => 0.1,
                ]
            ],
            'labels' => $labels,
        ];
    }

    protected function getHistoricData()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        $today = Carbon::now();

        // Obtener el tenant_id del usuario actual
        $tenantId = Auth::user()->tenant_id;

        $query = HuellaCarbono::select(
            DB::raw('DATE_FORMAT(fecha, "%Y-%m") as month'),
            DB::raw('SUM(total_emisiones) as total')
        )
            ->where('fecha', '>=', $sixMonthsAgo)
            ->where('fecha', '<=', $today);

        // Filtrar por tenant si el usuario tiene uno asignado
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } elseif (!Auth::user()->hasRole('superadmin')) {
            // Si no es superadmin y no tiene tenant, devolver vacío
            return [];
        }

        $data = $query->groupBy('month')
            ->orderBy('month')
            ->get();

        $result = [];
        foreach ($data as $item) {
            $result[$item->month] = round($item->total, 2);
        }

        return $result;
    }

    protected function calculateForecast($historicData)
    {
        // Si no hay datos históricos, devolver un arreglo vacío
        if (empty($historicData)) {
            return [];
        }

        // Extraer valores y calcular tendencia promedio
        $values = array_values($historicData);

        if (count($values) < 2) {
            // No hay suficientes datos para calcular tendencia
            $lastValue = end($values) ?: 0;
            $trend = 0;
        } else {
            // Calcular tendencia promedio
            $changes = [];
            for ($i = 1; $i < count($values); $i++) {
                $changes[] = $values[$i] - $values[$i - 1];
            }
            $trend = array_sum($changes) / count($changes);
            $lastValue = end($values);
        }

        // Proyectar próximos 3 meses
        $forecast = [];
        $lastMonth = array_key_last($historicData);

        // Si no hay meses históricos, no podemos hacer proyección
        if (empty($lastMonth)) {
            return [];
        }

        for ($i = 1; $i <= 3; $i++) {
            $nextMonth = Carbon::parse($lastMonth)->addMonths($i)->format('Y-m');
            $forecastValue = $lastValue + ($trend * $i);
            $forecast[$nextMonth] = max(0, round($forecastValue, 2)); // Asegurar que no sea negativo
        }

        return $forecast;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
