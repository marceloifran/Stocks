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
        'disponible_para_asignacion',
        'observaciones_obra'
    ];

    protected $casts = [
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
     * Verificar si está disponible para asignación
     */
    public function estaDisponible(): bool
    {
        return $this->disponible_para_asignacion && $this->obra_actual_id === null;
    }

    /**
     * Asignar a una obra
     */
    public function asignarAObra(Obra $obra): void
    {
        $this->update([
            'obra_actual_id' => $obra->id,
            'disponible_para_asignacion' => false
        ]);
    }

    /**
     * Finalizar asignación a obra
     */
    public function finalizarAsignacion(): void
    {
        $this->update([
            'obra_actual_id' => null,
            'disponible_para_asignacion' => true
        ]);
    }

    /**
     * Scopes
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible_para_asignacion', true)
            ->whereNull('obra_actual_id');
    }

    public function scopePorObra($query, $obraId)
    {
        return $query->where('obra_actual_id', $obraId);
    }
}
