<?php

namespace App\Console\Commands;

use App\Models\personal;
use App\Models\Roster;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActualizarRosters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rosters:actualizar {--dry-run : Ejecutar en modo de prueba sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza automáticamente los estados de rosters y crea nuevos ciclos cuando es necesario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 Ejecutando en modo de prueba (dry-run)...');
        }

        $this->info('🚀 Iniciando actualización de rosters...');

        // Actualizar estados de rosters existentes
        $this->actualizarEstadosRosters($dryRun);

        // Procesar rotaciones necesarias
        $this->procesarRotaciones($dryRun);

        // Crear nuevos ciclos automáticamente
        $this->crearNuevosCiclos($dryRun);

        $this->info('✅ Actualización de rosters completada.');
    }

    private function actualizarEstadosRosters(bool $dryRun): void
    {
        $this->info('📊 Actualizando estados de rosters...');

        $rosters = Roster::where('activo', true)
            ->whereIn('estado_actual', ['trabajando', 'descansando'])
            ->get();

        $actualizados = 0;

        foreach ($rosters as $roster) {
            $estadoAnterior = $roster->estado_actual;

            if (!$dryRun) {
                $roster->actualizarEstado();
            }

            if ($roster->estado_actual !== $estadoAnterior) {
                $actualizados++;
                $this->line("  - {$roster->personal->nombre}: {$estadoAnterior} → {$roster->estado_actual}");
            }
        }

        $this->info("✅ {$actualizados} rosters actualizados.");
    }

    private function procesarRotaciones(bool $dryRun): void
    {
        $this->info('🔄 Procesando rotaciones necesarias...');

        $personalNecesitaRotacion = personal::whereNotNull('proxima_rotacion')
            ->whereDate('proxima_rotacion', '<=', Carbon::today())
            ->with('obraActual')
            ->get();

        if ($personalNecesitaRotacion->isEmpty()) {
            $this->info('✅ No hay personal que necesite rotación.');
            return;
        }

        foreach ($personalNecesitaRotacion as $persona) {
            $this->line("  - Procesando rotación para: {$persona->nombre}");

            if (!$dryRun) {
                if ($persona->estaTrabajando()) {
                    $persona->iniciarDescanso();
                    $this->line("    → Cambiado a período de descanso");
                } else {
                    $persona->update([
                        'estado_roster' => 'trabajando',
                        'proxima_rotacion' => Carbon::now()->addDays(14)
                    ]);
                    $this->line("    → Cambiado a período de trabajo");
                }
            } else {
                $accion = $persona->estaTrabajando() ? 'descanso' : 'trabajo';
                $this->line("    → Se cambiaría a período de {$accion}");
            }
        }

        $this->info("✅ {$personalNecesitaRotacion->count()} rotaciones procesadas.");
    }

    private function crearNuevosCiclos(bool $dryRun): void
    {
        $this->info('🆕 Creando nuevos ciclos automáticamente...');

        $rostersFinalizados = Roster::where('activo', true)
            ->where('estado_actual', 'finalizado')
            ->whereDate('fecha_fin_descanso', '<=', Carbon::today())
            ->with(['personal', 'obra'])
            ->get();

        if ($rostersFinalizados->isEmpty()) {
            $this->info('✅ No hay rosters que necesiten nuevos ciclos.');
            return;
        }

        $ciclosCreados = 0;

        foreach ($rostersFinalizados as $roster) {
            // Verificar si el personal sigue asignado a la obra
            if ($roster->personal->obra_actual_id !== $roster->obra_id) {
                $this->line("  - {$roster->personal->nombre}: No crear nuevo ciclo (ya no asignado a {$roster->obra->nombre})");
                continue;
            }

            $this->line("  - Creando nuevo ciclo para: {$roster->personal->nombre} en {$roster->obra->nombre}");

            if (!$dryRun) {
                $nuevoCiclo = $roster->crearProximoCiclo();

                // Actualizar el personal
                $roster->personal->update([
                    'estado_roster' => 'trabajando',
                    'proxima_rotacion' => $nuevoCiclo->fecha_fin_trabajo,
                ]);

                // Desactivar el roster anterior
                $roster->update(['activo' => false]);

                $ciclosCreados++;
                $this->line("    → Nuevo ciclo #{$nuevoCiclo->ciclo_numero} creado");
            } else {
                $proximoCiclo = $roster->calcularProximoCiclo();
                $this->line("    → Se crearía ciclo #{$proximoCiclo['ciclo_numero']}");
                $ciclosCreados++;
            }
        }

        $this->info("✅ {$ciclosCreados} nuevos ciclos creados.");
    }
}
