<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class asistencia extends Model
{
    protected $fillable = ['fecha', 'hora', 'estado','codigo','presente'];

    protected $cast = [
        'presente' => 'boolean',
    ];


public function personal()
{
    return $this->belongsTo(personal::class, 'codigo', 'nro_identificacion');
}
public static function calcularHoras($asistencias)
{
    $horasTrabajadas = 0;

    foreach ($asistencias as $asistencia) {
        if ($asistencia->estado == 'entrada') {
            $entrada = Carbon::createFromFormat('H:i', $asistencia->hora);
        } elseif ($asistencia->estado == 'salida' && isset($entrada)) {
            $salida = Carbon::createFromFormat('H:i', $asistencia->hora);
            $horasTrabajadas += $salida->diffInHours($entrada);
            unset($entrada);
        }
    }

    return $horasTrabajadas;
}


}
