<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear roles y permisos en caché
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos para gestión de usuarios
        $userPermissions = [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
        ];

        // Crear todos los permisos si no existen ya
        $allPermissions = $userPermissions;
        foreach ($allPermissions as $permission) {
            // Verificar si el permiso ya existe
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Crear rol superadmin si no existe
        $superadminRole = Role::where('name', 'superadmin')->first();
        if (!$superadminRole) {
            $superadminRole = Role::create(['name' => 'superadmin']);
            // Asignar todos los permisos al superadmin
            $superadminRole->givePermissionTo($allPermissions);
        } else {
            // Actualizar permisos para asegurarse de que tiene todos
            $superadminRole->syncPermissions($allPermissions);
        }
    }
}
