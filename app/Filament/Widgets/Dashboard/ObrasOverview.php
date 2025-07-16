<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Obra;
use App\Models\personal;
use App\Models\Roster;
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
        $personalTrabajando = personal::where('estado_roster', 'trabajando')->count();
        $personalDescansando = personal::where('estado_roster', 'descansando')->count();
        $personalDisponible = personal::where('disponible_para_asignacion', true)
            ->where('estado_roster', '!=', 'trabajando')
            ->count();

        // Estadísticas de rosters
        $rostersActivos = Roster::where('activo', true)->count();
        $personalNecesitaRotacion = personal::whereNotNull('proxima_rotacion')
            ->whereDate('proxima_rotacion', '<=', now())
            ->count();

        // Gráficos de tendencia (últimos 7 días)
        $obrasChart = $this->getObrasChartData();
        $personalChart = $this->getPersonalChartData();
        $rostersChart = $this->getRostersChartData();

        return [
            Stat::make('Total de Obras', $totalObras)
                ->description("{$obrasActivas} activas, {$obrasEnProgreso} en progreso")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart($obrasChart),

            Stat::make('Personal Trabajando', $personalTrabajando)
                ->description("De {$personalTotal} total")
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($personalChart),

            Stat::make('Personal Descansando', $personalDescansando)
                ->description("{$personalDisponible} disponibles para asignación")
                ->descriptionIcon('heroicon-m-moon')
                ->color('warning')
                ->chart($this->getDescansoChartData()),

            Stat::make('Rosters Activos', $rostersActivos)
                ->description($personalNecesitaRotacion > 0 ? "{$personalNecesitaRotacion} necesitan rotación" : "Todas las rotaciones al día")
                ->descriptionIcon($personalNecesitaRotacion > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->descriptionColor($personalNecesitaRotacion > 0 ? 'danger' : 'success')
                ->color('info')
                ->chart($rostersChart),
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
            // Simular datos de personal trabajando por día
            $baseCount = personal::where('estado_roster', 'trabajando')->count();
            $variation = rand(-5, 5);
            $data[] = max(0, $baseCount + $variation);
        }
        return $data;
    }

    private function getDescansoChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            // Simular datos de personal descansando por día
            $baseCount = personal::where('estado_roster', 'descansando')->count();
            $variation = rand(-3, 3);
            $data[] = max(0, $baseCount + $variation);
        }
        return $data;
    }

    private function getRostersChartData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Roster::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}
