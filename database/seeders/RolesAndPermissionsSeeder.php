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

        // Crear permisos para gestión de tenants
        $tenantPermissions = [
            'view_any_tenant',
            'view_tenant',
            'create_tenant',
            'update_tenant',
            'delete_tenant',
        ];

        // Crear permisos para gestión de usuarios
        $userPermissions = [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
        ];

        // Crear permisos para gestión de Huella de Carbono
        $huellaCarbonoPermissions = [
            'view_any_huella_carbono',
            'view_huella_carbono',
            'create_huella_carbono',
            'update_huella_carbono',
            'delete_huella_carbono',
        ];

        // Crear todos los permisos
        $allPermissions = array_merge($tenantPermissions, $userPermissions, $huellaCarbonoPermissions);
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear rol superadmin
        $superadminRole = Role::create(['name' => 'superadmin']);
        // Asignar todos los permisos al superadmin
        $superadminRole->givePermissionTo($allPermissions);

        // Crear rol usuario
        $userRole = Role::create(['name' => 'usuario']);
        // Los usuarios normales solo pueden gestionar huella de carbono
        $userRole->givePermissionTo($huellaCarbonoPermissions);

        // Crear un usuario superadmin predeterminado
        // Esto lo haremos en un seeder separado
    }
}
