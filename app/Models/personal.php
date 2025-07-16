<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class personal extends Model
{
    protected $table = 'personals';
    protected $fillable = [
        'nombre',
        'dni',
        'firma',
        'nro_identificacion',
        'departamento',
        'obra_actual_id',
        'tipo_roster',
        'fecha_inicio_roster',
        'estado_roster',
        'proxima_rotacion',
        'dias_trabajados_consecutivos',
        'dias_descanso_consecutivos',
        'disponible_para_asignacion',
        'observaciones_roster'
    ];

    protected $casts = [
        'fecha_inicio_roster' => 'date',
        'proxima_rotacion' => 'date',
        'disponible_para_asignacion' => 'boolean'
    ];

    public function presente()
    {
        $asistencia = asistencia::where('codigo', $this->nro_identificacion)->exists();
        $presente = $asistencia ? true : false;

        return $presente;
    }

    public function asistencia()
    {
        return $this->hasMany(asistencia::class, 'codigo', 'nro_identificacion');
    }

    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }

    public function comidas()
    {
        return $this->hasMany(Comida::class, 'codigo', 'nro_identificacion');
    }

    /**
     * Relación con la obra actual
     */
    public function obraActual(): BelongsTo
    {
        return $this->belongsTo(Obra::class, 'obra_actual_id');
    }

    /**
     * Relación con todos los rosters
     */
    public function rosters(): HasMany
    {
        return $this->hasMany(Roster::class);
    }

    /**
     * Relación con rosters activos
     */
    public function rostersActivos(): HasMany
    {
        return $this->hasMany(Roster::class)->where('activo', true);
    }

    /**
     * Obtener el roster actual
     */
    public function rosterActual()
    {
        return $this->rosters()
            ->where('activo', true)
            ->where(function ($query) {
                $hoy = Carbon::today();
                $query->where(function ($q) use ($hoy) {
                    $q->where('fecha_inicio_trabajo', '<=', $hoy)
                        ->where('fecha_fin_trabajo', '>=', $hoy);
                })->orWhere(function ($q) use ($hoy) {
                    $q->where('fecha_inicio_descanso', '<=', $hoy)
                        ->where('fecha_fin_descanso', '>=', $hoy);
                });
            })
            ->first();
    }

    /**
     * Verificar si está trabajando actualmente
     */
    public function estaTrabajando(): bool
    {
        return $this->estado_roster === 'trabajando';
    }

    /**
     * Verificar si está descansando actualmente
     */
    public function estaDescansando(): bool
    {
        return $this->estado_roster === 'descansando';
    }

    /**
     * Verificar si está disponible para asignación
     */
    public function estaDisponible(): bool
    {
        return $this->disponible_para_asignacion && $this->estado_roster !== 'trabajando';
    }

    /**
     * Asignar a una obra con roster 14x14
     */
    public function asignarAObra(Obra $obra, Carbon $fechaInicio = null): Roster
    {
        $fechaInicio = $fechaInicio ?? Carbon::today();

        // Actualizar el personal
        $this->update([
            'obra_actual_id' => $obra->id,
            'estado_roster' => 'trabajando',
            'fecha_inicio_roster' => $fechaInicio,
            'proxima_rotacion' => $fechaInicio->copy()->addDays(13), // 14 días de trabajo
            'dias_trabajados_consecutivos' => 0,
            'dias_descanso_consecutivos' => 0,
            'disponible_para_asignacion' => false
        ]);

        // Crear el roster
        return Roster::create([
            'personal_id' => $this->id,
            'obra_id' => $obra->id,
            'fecha_inicio_trabajo' => $fechaInicio,
            'fecha_fin_trabajo' => $fechaInicio->copy()->addDays(13),
            'fecha_inicio_descanso' => $fechaInicio->copy()->addDays(14),
            'fecha_fin_descanso' => $fechaInicio->copy()->addDays(27),
            'estado_actual' => 'trabajando',
            'ciclo_numero' => 1,
            'activo' => true
        ]);
    }

    /**
     * Cambiar a período de descanso
     */
    public function iniciarDescanso(): void
    {
        $this->update([
            'estado_roster' => 'descansando',
            'dias_trabajados_consecutivos' => 0,
            'dias_descanso_consecutivos' => 0
        ]);

        // Actualizar roster actual
        $rosterActual = $this->rosterActual();
        if ($rosterActual) {
            $rosterActual->update(['estado_actual' => 'descansando']);
        }
    }

    /**
     * Finalizar asignación a obra
     */
    public function finalizarAsignacion(): void
    {
        $this->update([
            'obra_actual_id' => null,
            'estado_roster' => 'inactivo',
            'proxima_rotacion' => null,
            'dias_trabajados_consecutivos' => 0,
            'dias_descanso_consecutivos' => 0,
            'disponible_para_asignacion' => true
        ]);

        // Desactivar rosters activos
        $this->rostersActivos()->update(['activo' => false]);
    }

    /**
     * Actualizar días trabajados/descansados
     */
    public function actualizarContadores(): void
    {
        if ($this->estaTrabajando()) {
            $this->increment('dias_trabajados_consecutivos');
            $this->update(['dias_descanso_consecutivos' => 0]);
        } elseif ($this->estaDescansando()) {
            $this->increment('dias_descanso_consecutivos');
            $this->update(['dias_trabajados_consecutivos' => 0]);
        }
    }

    /**
     * Verificar si necesita rotación
     */
    public function necesitaRotacion(): bool
    {
        if (!$this->proxima_rotacion) {
            return false;
        }

        return Carbon::today()->gte($this->proxima_rotacion);
    }

    /**
     * Obtener información del roster actual
     */
    public function getInfoRoster(): array
    {
        $roster = $this->rosterActual();

        if (!$roster) {
            return [
                'tiene_roster' => false,
                'estado' => $this->estado_roster,
                'obra' => $this->obraActual?->nombre ?? 'Sin asignar'
            ];
        }

        return [
            'tiene_roster' => true,
            'estado' => $this->estado_roster,
            'obra' => $this->obraActual?->nombre ?? 'Sin asignar',
            'ciclo_numero' => $roster->ciclo_numero,
            'dias_restantes_trabajo' => $roster->diasRestantesTrabajo(),
            'dias_restantes_descanso' => $roster->diasRestantesDescanso(),
            'fecha_inicio_trabajo' => $roster->fecha_inicio_trabajo,
            'fecha_fin_trabajo' => $roster->fecha_fin_trabajo,
            'fecha_inicio_descanso' => $roster->fecha_inicio_descanso,
            'fecha_fin_descanso' => $roster->fecha_fin_descanso,
            'necesita_rotacion' => $this->necesitaRotacion()
        ];
    }

    /**
     * Scopes
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible_para_asignacion', true)
            ->where('estado_roster', '!=', 'trabajando');
    }

    public function scopeTrabajando($query)
    {
        return $query->where('estado_roster', 'trabajando');
    }

    public function scopeDescansando($query)
    {
        return $query->where('estado_roster', 'descansando');
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_actual_id', $obraId);
    }

    public function scopeConRoster($query, $tipoRoster = null)
    {
        $query = $query->whereNotNull('fecha_inicio_roster');

        if ($tipoRoster) {
            $query->where('tipo_roster', $tipoRoster);
        }

        return $query;
    }
}
