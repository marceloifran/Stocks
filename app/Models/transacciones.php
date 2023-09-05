<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transacciones extends Model
{
    protected $table = 'transacciones';

    protected $fillable = [
        'cuenta_id',
        'monto',
        'descripcion',
        'fecha',
        'tipo',
        // Otros campos de la transacción financiera
    ];
    protected static function booted()
    {
        static::creating(function ($transaccion) {
            $cuenta = Cuenta::findOrFail($transaccion->cuenta_id);

            if ($transaccion->tipo === 'Gasto') {
                $cuenta->saldo -= $transaccion->monto;
            } elseif ($transaccion->tipo === 'Ingreso') {
                $cuenta->saldo += $transaccion->monto;
            }

            $cuenta->save();
        });

        static::created(function ($transaccion) {
            // Aquí puedes realizar cualquier otra acción después de crear la transacción
        });
    }

    // Relaciones
    public function cuenta()
    {
        return $this->belongsTo(cuenta::class);
    }
}
