<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asistencia extends Model
{
    protected $fillable = ['codigo', 'fecha', 'hora', 'estado'];

    // Definir la relación con el modelo Personal
   // app/Models/Asistencia.php

public function personal()
{
    return $this->belongsTo(Personal::class, 'nro_identificacion', 'nro_identificacion');
}

}
