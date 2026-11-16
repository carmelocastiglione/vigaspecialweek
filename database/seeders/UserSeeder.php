<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the users table.
     */
    public function run(): void
    {
        $schoolClass = SchoolClass::query()->where('internal_id', '1A')->firstOrFail();
        $department = Department::query()->where('internal_id', 'INF')->firstOrFail();

        $student = User::factory()->create([
            'name' => 'Studente',
            'surname' => 'Studente',
            'email' => 'studente@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ]);
        $student->assignRole('student');
        $student->studentProfile()->create([
            'school_class_id' => $schoolClass->id,
        ]);

        $teacher = User::factory()->create([
            'name' => 'Docente',
            'surname' => 'Docente',
            'email' => 'docente@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ]);
        $teacher->assignRole('teacher');
        $teacher->teacherProfile()->create([
            'department_id' => $department->id,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Admin',
            'email' => 'admin@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Carmelo',
            'surname' => 'Castiglione',
            'email' => 'carmelo.c.castiglione@gmail.com',
            'password' => null,
        ])->assignRole('admin');
    }
}
