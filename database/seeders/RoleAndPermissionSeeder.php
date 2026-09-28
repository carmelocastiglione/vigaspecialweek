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
        // ========== RESET CACHE PERMESSI ==========
        app()['cache']->forget('spatie.permission.cache');

         // ========== PERMESSI GENERALI ==========
        Permission::firstOrCreate(['name' => 'admin.site']);

        // ========== PERMESSI PER GESTIONE UTENTI ==========
        Permission::firstOrCreate(['name' => 'users.view']);
        Permission::firstOrCreate(['name' => 'users.create']);
        Permission::firstOrCreate(['name' => 'users.edit']);
        Permission::firstOrCreate(['name' => 'users.delete']);
        Permission::firstOrCreate(['name' => 'users.manage_roles']);

        // ========== PERMESSI PER GESTIONE DIPARTIMENTI ==========
        Permission::firstOrCreate(['name' => 'departments.view']);
        Permission::firstOrCreate(['name' => 'departments.create']);
        Permission::firstOrCreate(['name' => 'departments.edit']);
        Permission::firstOrCreate(['name' => 'departments.delete']);

        // ========== PERMESSI PER GESTIONE CATEGORIE ==========
        Permission::firstOrCreate(['name' => 'categories.view']);
        Permission::firstOrCreate(['name' => 'categories.create']);
        Permission::firstOrCreate(['name' => 'categories.edit']);
        Permission::firstOrCreate(['name' => 'categories.delete']);

        // ========== RUOLI ==========
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);

        // ========== ASSEGNA PERMESSI AI RUOLI ==========
        // Admin ha TUTTI i permessi
        $adminRole->syncPermissions([
            'admin.site',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage_roles',
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete'
        ]);

        // Student e Teacher non hanno permessi per gli utenti
        $studentRole->syncPermissions([]);
        $teacherRole->syncPermissions([]);
    }
}
