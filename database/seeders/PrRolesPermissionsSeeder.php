<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PrRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view pr',
            'create pr',
            'edit pr',
            'delete pr',
            
            'access pr library',
            'access pr signatory',
            'access pr budget',
        ];
 
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'PR admin']);
        $adminRole->givePermissionTo(Permission::all());

        $encoderRole = Role::create(['name' => 'PR encoder']);
        $encoderRole->givePermissionTo(['view pr', 'create pr', 'edit pr', 'delete pr', 'access pr library', 'access pr signatory']);

        $viewerRole = Role::create(['name' => 'PR viewer']);
        $viewerRole->givePermissionTo(['view pr']);
    }
}
