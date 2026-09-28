<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creazione di 10 dipartimenti fittizi
        // Department::factory(10)->create();

        Department::create([
            'description' => 'Informatica',
            'internal_id' => 'INF',
        ]);

        Department::create([
            'description' => 'Matematica',
            'internal_id' => 'MAT',
        ]);

        Department::create([
            'description' => 'Scienze Naturali',
            'internal_id' => 'SCI',
        ]);

        Department::create([
            'description' => 'Lettere',
            'internal_id' => 'LET',
        ]);

        Department::create([
            'description' => 'Lingue',
            'internal_id' => 'LIN',
        ]);

        Department::create([
            'description' => 'Elettronica',
            'internal_id' => 'ELE',
        ]);

        Department::create([
            'description' => 'Diritto',
            'internal_id' => 'DIR',
        ]);

        Department::create([
            'description' => 'Economia',
            'internal_id' => 'ECO',
        ]);

        Department::create([
            'description' => 'Religione',
            'internal_id' => 'REL',
        ]);
    }
}
