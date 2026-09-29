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
        Permission::firstOrCreate(['name' => 'users.restore']);
        Permission::firstOrCreate(['name' => 'users.forceDelete']);

        // ========== PERMESSI PER GESTIONE DIPARTIMENTI ==========
        Permission::firstOrCreate(['name' => 'departments.view']);
        Permission::firstOrCreate(['name' => 'departments.create']);
        Permission::firstOrCreate(['name' => 'departments.edit']);
        Permission::firstOrCreate(['name' => 'departments.delete']);
        Permission::firstOrCreate(['name' => 'departments.restore']);
        Permission::firstOrCreate(['name' => 'departments.forceDelete']);   

        // ========== PERMESSI PER GESTIONE CATEGORIE ==========
        Permission::firstOrCreate(['name' => 'categories.view']);
        Permission::firstOrCreate(['name' => 'categories.create']);
        Permission::firstOrCreate(['name' => 'categories.edit']);
        Permission::firstOrCreate(['name' => 'categories.delete']);
        Permission::firstOrCreate(['name' => 'categories.restore']);
        Permission::firstOrCreate(['name' => 'categories.forceDelete']);

        // ========== PERMESSI PER GESTIONE AULE ==========
        Permission::firstOrCreate(['name' => 'classrooms.view']);
        Permission::firstOrCreate(['name' => 'classrooms.create']);
        Permission::firstOrCreate(['name' => 'classrooms.edit']);
        Permission::firstOrCreate(['name' => 'classrooms.delete']);
        Permission::firstOrCreate(['name' => 'classrooms.restore']);
        Permission::firstOrCreate(['name' => 'classrooms.forceDelete']);

        // ========== PERMESSI PER GESTIONE CLASSI ==========
        Permission::firstOrCreate(['name' => 'school-classes.view']);
        Permission::firstOrCreate(['name' => 'school-classes.create']);
        Permission::firstOrCreate(['name' => 'school-classes.edit']);
        Permission::firstOrCreate(['name' => 'school-classes.delete']);
        Permission::firstOrCreate(['name' => 'school-classes.restore']);
        Permission::firstOrCreate(['name' => 'school-classes.forceDelete']);

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
            'users.restore',
            'users.forceDelete',
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'departments.restore',
            'departments.forceDelete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'categories.restore',
            'categories.forceDelete',
            'classrooms.view',
            'classrooms.create',
            'classrooms.edit',
            'classrooms.delete',
            'classrooms.restore',
            'classrooms.forceDelete',
            'school-classes.view',
            'school-classes.create',
            'school-classes.edit',
            'school-classes.delete',
            'school-classes.restore',
            'school-classes.forceDelete',
        ]);

        // Student e Teacher non hanno permessi per gli utenti
        $studentRole->syncPermissions([]);
        $teacherRole->syncPermissions([]);
    }
}
