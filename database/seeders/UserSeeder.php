<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the users table.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Studente',
            'surname' => 'Studente',
            'email' => 'studente@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ])->assignRole('student');

        User::factory()->create([
            'name' => 'Docente',
            'surname' => 'Docente',
            'email' => 'docente@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ])->assignRole('teacher');

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
