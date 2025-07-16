<?php

namespace App\Filament\Pages;

use App\Models\Obra;
use App\Models\personal;
use App\Models\Roster;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardObras extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Dashboard Obras';
    protected static ?string $title = 'Dashboard de Obras y Rosters';
    protected static ?string $navigationGroup = 'Gestión de Obras';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.dashboard-obras';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Dashboard\ObrasOverview::class,
        ];
    }

    public function getViewData(): array
    {
        return [
            'obrasActivas' => $this->getObrasActivas(),
            'personalPorObra' => $this->getPersonalPorObra(),
            'proximasRotaciones' => $this->getProximasRotaciones(),
            'estadisticasGenerales' => $this->getEstadisticasGenerales(),
        ];
    }

    private function getObrasActivas()
    {
        return Obra::where('activa', true)
            ->with(['personalActual' => function ($query) {
                $query->select('id', 'nombre', 'obra_actual_id', 'estado_roster', 'proxima_rotacion');
            }])
            ->get()
            ->map(function ($obra) {
                return [
                    'id' => $obra->id,
                    'nombre' => $obra->nombre,
                    'codigo' => $obra->codigo,
                    'estado' => $obra->estado,
                    'cliente' => $obra->cliente,
                    'ubicacion' => $obra->ubicacion,
                    'personal_total' => $obra->personalActual->count(),
                    'personal_trabajando' => $obra->personalActual->where('estado_roster', 'trabajando')->count(),
                    'personal_descansando' => $obra->personalActual->where('estado_roster', 'descansando')->count(),
                    'fecha_inicio' => $obra->fecha_inicio,
                    'dias_transcurridos' => $obra->diasTranscurridos(),
                ];
            });
    }

    private function getPersonalPorObra()
    {
        return personal::whereNotNull('obra_actual_id')
            ->with(['obraActual:id,nombre,codigo'])
            ->get()
            ->map(function ($persona) {
                $rosterInfo = $persona->getInfoRoster();
                return [
                    'id' => $persona->id,
                    'nombre' => $persona->nombre,
                    'obra' => $persona->obraActual->nombre ?? 'Sin asignar',
                    'obra_codigo' => $persona->obraActual->codigo ?? '',
                    'estado_roster' => $persona->estado_roster,
                    'tipo_roster' => $persona->tipo_roster,
                    'proxima_rotacion' => $persona->proxima_rotacion,
                    'dias_trabajados' => $persona->dias_trabajados_consecutivos,
                    'dias_descanso' => $persona->dias_descanso_consecutivos,
                    'necesita_rotacion' => $rosterInfo['necesita_rotacion'] ?? false,
                ];
            });
    }

    private function getProximasRotaciones()
    {
        return personal::whereNotNull('proxima_rotacion')
            ->where('proxima_rotacion', '>=', now())
            ->orderBy('proxima_rotacion')
            ->with(['obraActual:id,nombre'])
            ->take(10)
            ->get()
            ->map(function ($persona) {
                return [
                    'nombre' => $persona->nombre,
                    'obra' => $persona->obraActual->nombre ?? 'Sin asignar',
                    'fecha_rotacion' => $persona->proxima_rotacion,
                    'dias_restantes' => now()->diffInDays($persona->proxima_rotacion),
                    'estado_actual' => $persona->estado_roster,
                    'proximo_estado' => $persona->estado_roster === 'trabajando' ? 'descansando' : 'trabajando',
                ];
            });
    }

    private function getEstadisticasGenerales()
    {
        return [
            'obras_total' => Obra::count(),
            'obras_activas' => Obra::where('activa', true)->count(),
            'obras_en_progreso' => Obra::where('estado', 'en_progreso')->count(),
            'personal_total' => personal::count(),
            'personal_asignado' => personal::whereNotNull('obra_actual_id')->count(),
            'personal_trabajando' => personal::where('estado_roster', 'trabajando')->count(),
            'personal_descansando' => personal::where('estado_roster', 'descansando')->count(),
            'personal_disponible' => personal::where('disponible_para_asignacion', true)
                ->where('estado_roster', '!=', 'trabajando')
                ->count(),
            'rosters_activos' => Roster::where('activo', true)->count(),
            'rotaciones_pendientes' => personal::whereNotNull('proxima_rotacion')
                ->whereDate('proxima_rotacion', '<=', now())
                ->count(),
        ];
    }
}
