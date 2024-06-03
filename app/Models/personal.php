<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class personal extends Model
{
    protected $fillable = [
        'nombre',
        'rol',
        'fecha_entrada',
        'dni',
        'firma',
        'nro_identificacion'
    ];

    public function presente()
    {
        // Verificar si existen registros de asistencia para la persona actual
        $asistencia = Asistencia::where('codigo', $this->nro_identificacion)->exists();

        // Si existe asistencia, la persona está presente, de lo contrario, no está presente
        $presente = $asistencia ? true : false;

        return $presente;
    }

    public function sueldo()
    {
        return $this->hasMany(sueldo::class, 'personal_id');
    }

    // app/Models/Personal.php

    public function asistencia()
    {
        return $this->hasMany(asistencia::class, 'codigo', 'nro_identificacion');
    }
    public function HorasGenerales()
    {
        return $this->hasMany(HorasGenerales::class, 'codigo', 'nro_identificacion');
    }

    public function permiso()
    {
        return $this->belongsToMany(permiso::class, 'permiso_personal');
    }
    public function checklists()
    {
        return $this->belongsToMany(checklists::class, 'checklist_personal', 'personal_id', 'checklist_id');
    }


    public function vacaciones()
    {
        return $this->hasMany(vacaciones::class, 'personal_id');
    }


    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }

    public function equipos()
    {
        return $this->hasMany(equipos::class, 'personal_id');
    }
}
