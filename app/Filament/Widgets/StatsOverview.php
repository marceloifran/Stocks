<?php

namespace App\Filament\Widgets;

use App\Models\Comida;
use App\Models\personal;
use App\Models\asistencia;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Total de personal
        $totalPersonal = personal::count();

        // Asistencias de hoy
        $asistenciasHoy = asistencia::whereDate('fecha', Carbon::today())
            ->where('estado', 'entrada')
            ->count();

        // Porcentaje de asistencia
        $porcentajeAsistencia = $totalPersonal > 0
            ? round(($asistenciasHoy / $totalPersonal) * 100, 1)
            : 0;

        // Total de comidas de hoy
        $comidasHoy = Comida::whereDate('fecha', Carbon::today())->count();

        // Comidas por tipo
        $desayunos = Comida::whereDate('fecha', Carbon::today())
            ->where('tipo_comida', 'desayuno')
            ->count();

        $almuerzos = Comida::whereDate('fecha', Carbon::today())
            ->where('tipo_comida', 'almuerzo')
            ->count();

        $meriendas = Comida::whereDate('fecha', Carbon::today())
            ->where('tipo_comida', 'merienda')
            ->count();

        $cenas = Comida::whereDate('fecha', Carbon::today())
            ->where('tipo_comida', 'cena')
            ->count();

        // Asistencias del último mes
        $asistenciasMes = asistencia::whereMonth('fecha', Carbon::now()->month)
            ->where('estado', 'entrada')
            ->count();

        // Días laborables en el mes actual
        $diasLaborables = Carbon::now()->daysInMonth - 8; // Aproximado, restando fines de semana

        // Porcentaje de asistencia mensual
        $asistenciaEsperadaMes = $totalPersonal * $diasLaborables;
        $porcentajeAsistenciaMes = $asistenciaEsperadaMes > 0
            ? round(($asistenciasMes / $asistenciaEsperadaMes) * 100, 1)
            : 0;

        // Calcular la tendencia (aumento o disminución) respecto al día anterior
        $asistenciasAyer = asistencia::whereDate('fecha', Carbon::yesterday())
            ->where('estado', 'entrada')
            ->count();

        $tendenciaAsistencia = $asistenciasAyer > 0
            ? round((($asistenciasHoy - $asistenciasAyer) / $asistenciasAyer) * 100, 1)
            : 0;

        $comidasAyer = Comida::whereDate('fecha', Carbon::yesterday())->count();
        $tendenciaComidas = $comidasAyer > 0
            ? round((($comidasHoy - $comidasAyer) / $comidasAyer) * 100, 1)
            : 0;

        return [
            Stat::make('Total Personal', $totalPersonal)
                ->description('Total de personal registrado')
                ->descriptionIcon('heroicon-o-users')
                ->chart([7, 8, 9, 8, 10, $totalPersonal])
                ->color('primary'),

            Stat::make('Asistencia Hoy', $asistenciasHoy)
                ->description($porcentajeAsistencia . '% del personal')
                ->descriptionIcon($tendenciaAsistencia >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->chart([5, 6, 7, 8, 6, $asistenciasHoy])
                ->color($porcentajeAsistencia >= 70 ? 'success' : ($porcentajeAsistencia >= 50 ? 'warning' : 'danger')),

            Stat::make('Comidas Hoy', $comidasHoy)
                ->description("D: $desayunos, A: $almuerzos, M: $meriendas, C: $cenas")
                ->descriptionIcon($tendenciaComidas >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->chart([3, 5, 7, 6, 4, $comidasHoy])
                ->color('success'),

            Stat::make('Asistencia Mensual', $asistenciasMes)
                ->description($porcentajeAsistenciaMes . '% de asistencia esperada')
                ->descriptionIcon('heroicon-o-calendar')
                ->chart([120, 140, 130, 150, 160, $asistenciasMes])
                ->color($porcentajeAsistenciaMes >= 70 ? 'success' : ($porcentajeAsistenciaMes >= 50 ? 'warning' : 'danger')),
        ];
    }
}
