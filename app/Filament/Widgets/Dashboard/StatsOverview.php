<?php

namespace App\Filament\Widgets\Dashboard;

use Carbon\Carbon;
use App\Models\personal;
use App\Models\asistencia;
use App\Models\Comida;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPersonal = personal::count();

        $today = Carbon::today();
        $asistenciasHoy = asistencia::whereDate('created_at', $today)->count();
        $porcentajeAsistencia = $totalPersonal > 0 ? round(($asistenciasHoy / $totalPersonal) * 100, 1) : 0;



        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $diasLaborables = $finMes->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $inicioMes);

        $asistenciasMes = asistencia::whereBetween('created_at', [$inicioMes, $finMes])->count();
        $asistenciasEsperadas = $totalPersonal * $diasLaborables;
        $porcentajeMensual = $asistenciasEsperadas > 0 ? round(($asistenciasMes / $asistenciasEsperadas) * 100, 1) : 0;

        // Generar datos de gráficos para los últimos 7 días
        $attendanceChart = $this->getAttendanceChartData();
        $personalChart = $this->getPersonalTrendData();
        $monthlyChart = $this->getMonthlyAttendanceData();

        return [
            Stat::make('Total Personal', $totalPersonal)
                ->description('Empleados registrados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-users')
                ->color('info')
                ->chart($personalChart),

            Stat::make('Asistencia Hoy', $asistenciasHoy)
                ->description($porcentajeAsistencia . '% del personal presente')
                ->descriptionIcon($porcentajeAsistencia >= 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($porcentajeAsistencia >= 80 ? 'success' : 'danger')
                ->icon('heroicon-o-clipboard-document-check')
                ->color($porcentajeAsistencia >= 80 ? 'success' : 'warning')
                ->chart($attendanceChart),


            Stat::make('Asistencia Mensual', $asistenciasMes)
                ->description($porcentajeMensual . '% de asistencia esperada')
                ->descriptionIcon($porcentajeMensual >= 85 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->descriptionColor($porcentajeMensual >= 85 ? 'success' : 'danger')
                ->icon('heroicon-o-calendar')
                ->color('primary')
                ->chart($monthlyChart),
        ];
    }

    private function getAttendanceChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $attendance = asistencia::whereDate('created_at', $date)->count();
            $totalPersonal = personal::count();
            $percentage = $totalPersonal > 0 ? round(($attendance / $totalPersonal) * 100) : 0;
            $data[] = $percentage;
        }
        return $data;
    }

    private function getMealsChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $meals = Comida::whereDate('created_at', $date)->count();
            $data[] = $meals;
        }
        return $data;
    }

    private function getPersonalTrendData(): array
    {
        // Simular crecimiento del personal en los últimos 7 días
        $baseCount = personal::count();
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $variation = rand(-2, 1); // Pequeña variación
            $data[] = max(0, $baseCount + $variation);
        }
        return $data;
    }

    private function getMonthlyAttendanceData(): array
    {
        $data = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonth = Carbon::now()->daysInMonth;

        for ($i = 0; $i < min(7, $daysInMonth); $i++) {
            $date = $startOfMonth->copy()->addDays($i * 4); // Cada 4 días
            if ($date <= Carbon::now()) {
                $attendance = asistencia::whereDate('created_at', $date)->count();
                $data[] = $attendance;
            }
        }

        return $data;
    }
}
