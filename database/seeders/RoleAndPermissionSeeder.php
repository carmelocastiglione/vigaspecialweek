<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache
        app()['cache']->forget('spatie.permission.cache');

        // Creare permessi
        Permission::firstOrCreate(['name' => 'manage users']);

        // Creare ruoli
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'student']);
        Role::firstOrCreate(['name' => 'teacher']);

        // Assegnare permessi ai ruoli
        // $editorRole->syncPermissions(['create articles', 'edit articles']);
        // $viewerRole->syncPermissions(['view articles']);

    }
}
