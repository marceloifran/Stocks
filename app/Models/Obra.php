<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Obra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'ubicacion',
        'cliente',
        'fecha_inicio',
        'fecha_fin_estimada',
        'estado',
        'presupuesto',
        'contactos',
        'activa'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin_estimada' => 'date',
        'contactos' => 'array',
        'presupuesto' => 'decimal:2',
        'activa' => 'boolean'
    ];

    /**
     * Relación con personal asignado actualmente
     */
    public function personalActual(): HasMany
    {
        return $this->hasMany(Personal::class, 'obra_actual_id');
    }

    /**
     * Relación con todos los rosters de esta obra
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
     * Obtener personal que está trabajando actualmente
     */
    public function personalTrabajando()
    {
        return $this->personalActual()->where('estado_roster', 'trabajando');
    }

    /**
     * Obtener personal que está descansando
     */
    public function personalDescansando()
    {
        return $this->personalActual()->where('estado_roster', 'descansando');
    }

    /**
     * Calcular días transcurridos desde el inicio
     */
    public function diasTranscurridos(): int
    {
        if (!$this->fecha_inicio) {
            return 0;
        }

        return Carbon::parse($this->fecha_inicio)->diffInDays(Carbon::now());
    }

    /**
     * Verificar si la obra está activa
     */
    public function estaActiva(): bool
    {
        return $this->activa && in_array($this->estado, ['planificada', 'en_progreso']);
    }

    /**
     * Obtener estadísticas de la obra
     */
    public function getEstadisticas(): array
    {
        $personalTotal = $this->personalActual()->count();
        $personalTrabajando = $this->personalTrabajando()->count();
        $personalDescansando = $this->personalDescansando()->count();

        return [
            'personal_total' => $personalTotal,
            'personal_trabajando' => $personalTrabajando,
            'personal_descansando' => $personalDescansando,
            'dias_transcurridos' => $this->diasTranscurridos(),
            'estado' => $this->estado,
            'activa' => $this->activa
        ];
    }

    /**
     * Scopes
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function scopePorCliente($query, $cliente)
    {
        return $query->where('cliente', 'like', "%{$cliente}%");
    }
}
