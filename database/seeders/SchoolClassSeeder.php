<?php

namespace Database\Seeders;

use App\Enums\Track;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolClass::create([
            'description' => '1A',
            'internal_id' => '1A',
            'year' => 1,
            'section' => 'A',
            'track' => Track::AFM,
        ]);

        SchoolClass::create([
            'description' => '1B',
            'internal_id' => '1B',
            'year' => 1,
            'section' => 'B',
            'track' => Track::AFM,
        ]);

        SchoolClass::create([
            'description' => '1C',
            'internal_id' => '1C',
            'year' => 1,
            'section' => 'C',
            'track' => Track::RIM,
        ]);

        SchoolClass::create([
            'description' => '1D',
            'internal_id' => '1D',
            'year' => 1,
            'section' => 'D',
            'track' => Track::RIM,
        ]);

        SchoolClass::create([
            'description' => '3G',
            'internal_id' => '3G',
            'year' => 3,
            'section' => 'G',
            'track' => Track::INFORMATICA,
        ]);

        SchoolClass::create([
            'description' => '3H',
            'internal_id' => '3H',
            'year' => 3,
            'section' => 'H',
            'track' => Track::INFORMATICA,
        ]);

        SchoolClass::create([
            'description' => '3I',
            'internal_id' => '3I',
            'year' => 3,
            'section' => 'I',
            'track' => Track::GRAFICA,
        ]);
    }
}
