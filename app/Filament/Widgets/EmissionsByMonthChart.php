<?php

namespace App\Filament\Widgets;

use App\Models\HuellaCarbono;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmissionsByMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Emisiones por Mes';
    protected static ?int $sort = 1;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'md:col-span-6';

    protected function getData(): array
    {
        // Obtener el tenant_id del usuario actual
        $tenantId = Auth::user()->tenant_id;

        $query = HuellaCarbono::select(
            DB::raw('MONTH(fecha) as mes'),
            DB::raw('YEAR(fecha) as año'),
            DB::raw('SUM(total_emisiones) as total')
        )
            ->whereYear('fecha', date('Y'));

        // Filtrar por tenant si el usuario tiene uno asignado
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } elseif (!Auth::user()->hasRole('superadmin')) {
            // Si no es superadmin y no tiene tenant, no mostrar datos
            return $this->getEmptyData();
        }

        // Verificar si hay registros antes de continuar
        if (HuellaCarbono::count() == 0) {
            return $this->getEmptyData();
        }

        $data = $query->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        // Si no hay datos, devolver una estructura vacía
        if ($data->isEmpty()) {
            return $this->getEmptyData();
        }

        $months = [];
        $emissions = [];

        foreach ($data as $item) {
            $monthName = Carbon::create()->month($item->mes)->locale('es')->monthName;
            $months[] = ucfirst($monthName);
            $emissions[] = round($item->total, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'kg CO2e',
                    'data' => $emissions,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(72, 187, 120, 0.2)',
                    'borderColor' => 'rgb(72, 187, 120)',
                    'pointBackgroundColor' => 'rgb(72, 187, 120)',
                    'tension' => 0.3,
                ]
            ],
            'labels' => $months,
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
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(72, 187, 120, 0.2)',
                    'borderColor' => 'rgb(72, 187, 120)',
                    'pointBackgroundColor' => 'rgb(72, 187, 120)',
                    'tension' => 0.3,
                ]
            ],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
