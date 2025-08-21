<?php

namespace App\Filament\Widgets;

use App\Models\HuellaCarbono;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EmissionsByMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Emisiones por Mes';
    protected static ?int $sort = 1;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = HuellaCarbono::select(
            DB::raw('MONTH(fecha) as mes'),
            DB::raw('YEAR(fecha) as año'),
            DB::raw('SUM(total_emisiones) as total')
        )
            ->whereYear('fecha', date('Y'))
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

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

    protected function getType(): string
    {
        return 'line';
    }
}
