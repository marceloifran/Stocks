<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class personal extends Model
{
    protected $fillable = [
        'nombre',
        'dni',
        'firma',
        'nro_identificacion'
    ];

    public function presente()
    {
        $asistencia = Asistencia::where('codigo', $this->nro_identificacion)->exists();
        $presente = $asistencia ? true : false;

        return $presente;
    }


    public function asistencia()
    {
        return $this->hasMany(asistencia::class, 'codigo', 'nro_identificacion');
    }
   

    public function permiso()
    {
        return $this->belongsToMany(permiso::class, 'permiso_personal');
    }
    public function checklists()
    {
        return $this->belongsToMany(checklists::class, 'checklist_personal', 'personal_id', 'checklist_id');
    }
    public function sueldos()
    {
        return $this->hasMany(Sueldo::class);
    }


    public function stockMovement()
    {
        return $this->hasMany(StockMovement::class, 'personal_id');
    }
    
    public function capacitaciones()
    {
        return $this->belongsToMany(capacitaciones::class, 'capacitacion_personal', 'personal_id', 'capacitacion_id');
    }
}
