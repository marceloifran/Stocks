<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sueldo extends Model
{
    use HasFactory;

    protected $fillable = [
        'personal_id',
        'mes',
        'anio',
        'horas_normales',
        'horas_extras',
        'pago_horas_normales',
        'pago_horas_extras',
        'total',
    ];

    public function personal()
    {
        return $this->belongsTo(personal::class);
    }

    public static function calcularHoras($asistencias)
    {
        $horas_normales = 0;
        $horas_extras = 0;
        $ultima_entrada = null;

        foreach ($asistencias as $asistencia) {
            if ($asistencia->estado == 'entrada') {
                $ultima_entrada = new \DateTime($asistencia->hora);
            } elseif ($asistencia->estado == 'salida' && $ultima_entrada) {
                $salida = new \DateTime($asistencia->hora);
                $interval = $ultima_entrada->diff($salida);
                $horas_trabajadas = $interval->h + ($interval->i / 60);

                for ($i = $ultima_entrada->format('H'); $i < $salida->format('H'); $i++) {
                    if ($i >= 7 && $i < 19) {
                        $horas_normales++;
                    } else {
                        $horas_extras++;
                    }
                }

                $ultima_entrada = null; // Resetear la última entrada después de calcular
            }
            
        }

        return [
            'horas_normales' => $horas_normales,
            'horas_extras' => $horas_extras,
        ];
    }
}
