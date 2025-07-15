<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\asistencia;
use App\Models\personal;
use Filament\Widgets\ChartWidget;

class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Tendencia de Asistencia (Últimos 30 días)';
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(29);

        $attendanceData = [];
        $labels = [];
        $totalPersonal = personal::count();

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dailyAttendance = asistencia::whereDate('created_at', $date)->count();
            $attendancePercentage = $totalPersonal > 0 ? round(($dailyAttendance / $totalPersonal) * 100, 1) : 0;

            $attendanceData[] = $attendancePercentage;
            $labels[] = $date->format('d/m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Porcentaje de Asistencia',
                    'data' => $attendanceData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(59, 130, 246)',
                    'pointBorderColor' => 'rgb(255, 255, 255)',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => 'white',
                    'bodyColor' => 'white',
                    'borderColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderWidth' => 1,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}
