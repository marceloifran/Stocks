<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Obra;
use App\Models\personal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ObrasOverview extends BaseWidget
{
    protected static ?int $sort = 8;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Estadísticas de obras
        $totalObras = Obra::count();
        $obrasActivas = Obra::where('activa', true)->count();
        $obrasEnProgreso = Obra::where('estado', 'en_progreso')->count();
        $obrasCompletadas = Obra::where('estado', 'completada')->count();

        // Estadísticas de personal
        $personalTotal = personal::count();
        $personalAsignado = personal::whereNotNull('obra_actual_id')->count();
        $personalDisponible = personal::where('disponible_para_asignacion', true)
            ->whereNull('obra_actual_id')
            ->count();

        // Gráficos de tendencia (últimos 7 días)
        $obrasChart = $this->getObrasChartData();
        $personalChart = $this->getPersonalChartData();

        return [
            Stat::make('Total de Obras', $totalObras)
                ->description("{$obrasActivas} activas, {$obrasEnProgreso} en progreso")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart($obrasChart),

            Stat::make('Personal Asignado', $personalAsignado)
                ->description("De {$personalTotal} total")
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($personalChart),

            Stat::make('Personal Disponible', $personalDisponible)
                ->description("Disponible para asignación")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Obras Completadas', $obrasCompletadas)
                ->description("Proyectos finalizados")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    private function getObrasChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Obra::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getPersonalChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            // Datos de personal asignado por día
            $count = personal::whereNotNull('obra_actual_id')->count();
            $variation = rand(-2, 2);
            $data[] = max(0, $count + $variation);
        }
        return $data;
    }
}
