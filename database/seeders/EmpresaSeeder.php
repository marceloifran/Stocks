<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresa = new \App\Models\Empresa();
        $empresa->nombre = 'Empresa 1';
        $empresa->cuit = '123456789';
        $empresa->direccion = 'Calle falsa 123';
        $empresa->cp = '4430';
        $empresa->localidad = 'Salta';
        $empresa->pcia = 'Salta Capital';
        $empresa->razon_social = 'Empresa 1 S.A.';
        $empresa->save();
    }
}
