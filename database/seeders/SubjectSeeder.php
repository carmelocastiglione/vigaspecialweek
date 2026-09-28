<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Department;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creazione di 10 materie fittizie
        // Subject::factory(10)->create();

        Subject::create([
            'description' => 'Matematica',
            'internal_id' => 'MAT',
            'department_id' => Department::where('description', 'Matematica')->first()->id
        ]);

        Subject::create([
            'description' => 'Informatica',
            'internal_id' => 'INF',
            'department_id' => Department::where('description', 'Informatica')->first()->id
        ]);

        Subject::create([
            'description' => 'Sistemi e Reti',
            'internal_id' => 'SIS',
            'department_id' => Department::where('description', 'Informatica')->first()->id
        ]);

        Subject::create([
            'description' => 'Fisica',
            'internal_id' => 'FIS',
            'department_id' => Department::where('description', 'Scienze Naturali')->first()->id
        ]);

        Subject::create([
            'description' => 'Chimica',
            'internal_id' => 'CHI',
            'department_id' => Department::where('description', 'Scienze Naturali')->first()->id
        ]);

        Subject::create([
            'description' => 'Biologia',
            'internal_id' => 'BIO',
            'department_id' => Department::where('description', 'Scienze Naturali')->first()->id
        ]);

        Subject::create([
            'description' => 'Italiano',
            'internal_id' => 'ITA',
            'department_id' => Department::where('description', 'Lettere')->first()->id
        ]);

        Subject::create([
            'description' => 'Storia',
            'internal_id' => 'STO',
            'department_id' => Department::where('description', 'Lettere')->first()->id
        ]);

        Subject::create([
            'description' => 'Inglese',
            'internal_id' => 'ING',
            'department_id' => Department::where('description', 'Lingue')->first()->id
        ]);

        Subject::create([
            'description' => 'Tedesco',
            'internal_id' => 'TED',
            'department_id' => Department::where('description', 'Lingue')->first()->id
        ]);

        Subject::create([
            'description' => 'Elettronica',
            'internal_id' => 'ELE',
            'department_id' => Department::where('description', 'Elettronica')->first()->id
        ]);

        Subject::create([
            'description' => 'Diritto',
            'internal_id' => 'DIR',
            'department_id' => Department::where('description', 'Diritto')->first()->id
        ]);

        Subject::create([
            'description' => 'Economia Aziendale',
            'internal_id' => 'ECO',
            'department_id' => Department::where('description', 'Economia')->first()->id
        ]);
    }
}
