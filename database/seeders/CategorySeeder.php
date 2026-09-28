<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creazione di 10 categorie fittizie
        // Category::factory(10)->create();

        Category::create([
            'description' => 'Attività culturali',
            'internal_id' => 'CUL',
        ]);

        Category::create([
            'description' => 'Attività sportive',
            'internal_id' => 'SPO',
        ]);

        Category::create([
            'description' => 'Attività artistiche',
            'internal_id' => 'ART',
        ]);

        Category::create([
            'description' => 'Attività ricreative',
            'internal_id' => 'REC',
        ]);

        Category::create([
            'description' => 'Attività sociali',
            'internal_id' => 'SOC',
        ]);

        Category::create([
            'description' => 'Attività educative',
            'internal_id' => 'EDU',
        ]);
    }
}
