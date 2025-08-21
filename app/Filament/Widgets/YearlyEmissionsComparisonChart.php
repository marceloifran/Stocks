<?php

namespace App\Filament\Widgets;

use App\Models\HuellaCarbono;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class YearlyEmissionsComparisonChart extends ChartWidget
{
    protected static ?string $heading = 'Comparativa de Emisiones Anuales';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;

        // Obtener datos para el año actual
        $currentYearData = $this->getYearData($currentYear);

        // Obtener datos para el año anterior
        $previousYearData = $this->getYearData($previousYear);

        // Meses para etiquetas
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = ucfirst(Carbon::create()->month($i)->locale('es')->monthName);
        }

        return [
            'datasets' => [
                [
                    'label' => $currentYear,
                    'data' => $currentYearData,
                    'backgroundColor' => 'rgba(72, 187, 120, 0.2)',
                    'borderColor' => 'rgb(72, 187, 120)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => $previousYear,
                    'data' => $previousYearData,
                    'backgroundColor' => 'rgba(66, 153, 225, 0.2)',
                    'borderColor' => 'rgb(66, 153, 225)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getYearData($year)
    {
        // Obtener el tenant_id del usuario actual
        $tenantId = Auth::user()->tenant_id;

        $query = HuellaCarbono::select(
            DB::raw('MONTH(fecha) as mes'),
            DB::raw('SUM(total_emisiones) as total')
        )
            ->whereYear('fecha', $year);

        // Filtrar por tenant si el usuario tiene uno asignado
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } elseif (!Auth::user()->hasRole('superadmin')) {
            // Si no es superadmin y no tiene tenant, no mostrar datos
            return array_fill(0, 12, 0);
        }

        $data = $query->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes');

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = isset($data[$i]) ? round($data[$i]->total, 2) : 0;
        }

        return $monthlyData;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
