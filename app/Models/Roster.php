<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Roster extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_id',
        'obra_id',
        'fecha_inicio_trabajo',
        'fecha_fin_trabajo',
        'fecha_inicio_descanso',
        'fecha_fin_descanso',
        'estado_actual',
        'ciclo_numero',
        'observaciones',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio_trabajo' => 'date',
        'fecha_fin_trabajo' => 'date',
        'fecha_inicio_descanso' => 'date',
        'fecha_fin_descanso' => 'date',
        'activo' => 'boolean'
    ];

    /**
     * Relación con Personal
     */
    public function personal(): BelongsTo
    {
        return $this->belongsTo(Personal::class);
    }

    /**
     * Relación con Obra
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    /**
     * Verificar si está en período de trabajo
     */
    public function estaTrabajando(): bool
    {
        $hoy = Carbon::today();
        return $hoy->between($this->fecha_inicio_trabajo, $this->fecha_fin_trabajo)
            && $this->estado_actual === 'trabajando';
    }

    /**
     * Verificar si está en período de descanso
     */
    public function estaDescansando(): bool
    {
        $hoy = Carbon::today();
        return $hoy->between($this->fecha_inicio_descanso, $this->fecha_fin_descanso)
            && $this->estado_actual === 'descansando';
    }

    /**
     * Obtener días restantes de trabajo
     */
    public function diasRestantesTrabajo(): int
    {
        if (!$this->estaTrabajando()) {
            return 0;
        }

        return Carbon::today()->diffInDays($this->fecha_fin_trabajo, false);
    }

    /**
     * Obtener días restantes de descanso
     */
    public function diasRestantesDescanso(): int
    {
        if (!$this->estaDescansando()) {
            return 0;
        }

        return Carbon::today()->diffInDays($this->fecha_fin_descanso, false);
    }

    /**
     * Calcular próximo ciclo de trabajo
     */
    public function calcularProximoCiclo(): array
    {
        $fechaFinDescanso = $this->fecha_fin_descanso;
        $proximoInicioTrabajo = $fechaFinDescanso->copy()->addDay();
        $proximoFinTrabajo = $proximoInicioTrabajo->copy()->addDays(13); // 14 días de trabajo
        $proximoInicioDescanso = $proximoFinTrabajo->copy()->addDay();
        $proximoFinDescanso = $proximoInicioDescanso->copy()->addDays(13); // 14 días de descanso

        return [
            'ciclo_numero' => $this->ciclo_numero + 1,
            'fecha_inicio_trabajo' => $proximoInicioTrabajo,
            'fecha_fin_trabajo' => $proximoFinTrabajo,
            'fecha_inicio_descanso' => $proximoInicioDescanso,
            'fecha_fin_descanso' => $proximoFinDescanso,
        ];
    }

    /**
     * Actualizar estado basado en la fecha actual
     */
    public function actualizarEstado(): void
    {
        $hoy = Carbon::today();

        if ($this->estaTrabajando()) {
            $this->estado_actual = 'trabajando';
        } elseif ($this->estaDescansando()) {
            $this->estado_actual = 'descansando';
        } elseif ($hoy->gt($this->fecha_fin_descanso)) {
            $this->estado_actual = 'finalizado';
        }

        $this->save();
    }

    /**
     * Crear próximo ciclo automáticamente
     */
    public function crearProximoCiclo(): self
    {
        $proximoCiclo = $this->calcularProximoCiclo();

        return self::create([
            'personal_id' => $this->personal_id,
            'obra_id' => $this->obra_id,
            'fecha_inicio_trabajo' => $proximoCiclo['fecha_inicio_trabajo'],
            'fecha_fin_trabajo' => $proximoCiclo['fecha_fin_trabajo'],
            'fecha_inicio_descanso' => $proximoCiclo['fecha_inicio_descanso'],
            'fecha_fin_descanso' => $proximoCiclo['fecha_fin_descanso'],
            'estado_actual' => 'trabajando',
            'ciclo_numero' => $proximoCiclo['ciclo_numero'],
            'activo' => true
        ]);
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeTrabajando($query)
    {
        return $query->where('estado_actual', 'trabajando');
    }

    public function scopeDescansando($query)
    {
        return $query->where('estado_actual', 'descansando');
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_id', $obraId);
    }

    public function scopeEnFecha($query, $fecha)
    {
        return $query->where(function ($q) use ($fecha) {
            $q->where(function ($subQ) use ($fecha) {
                $subQ->where('fecha_inicio_trabajo', '<=', $fecha)
                    ->where('fecha_fin_trabajo', '>=', $fecha);
            })->orWhere(function ($subQ) use ($fecha) {
                $subQ->where('fecha_inicio_descanso', '<=', $fecha)
                    ->where('fecha_fin_descanso', '>=', $fecha);
            });
        });
    }
}
