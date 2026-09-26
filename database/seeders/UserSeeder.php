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
        ]);

        User::factory()->create([
            'name' => 'Docente',
            'surname' => 'Docente',
            'email' => 'docente@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Admin',
            'email' => 'admin@issvigano.org',
            'password' => Hash::make('vigaspecialweek'),
        ]);
    }
}
