<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Enums\ClassroomFloor;
use App\Enums\ClassroomBuilding;
use App\Enums\ClassroomType;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classroom::create([
            'description' => 'T.01',
            'internal_id' => 'T.01',
            'floor' => ClassroomFloor::TERRA,
            'building' => ClassroomBuilding::SEDE_CENTRALE,
            'type' => ClassroomType::AULA,
        ]);

        Classroom::create([
            'description' => '1.03',
            'internal_id' => '1.03',
            'floor' => ClassroomFloor::PRIMO,
            'building' => ClassroomBuilding::SEDE_CENTRALE,
            'type' => ClassroomType::AULA,
        ]);

        Classroom::create([
            'description' => '2.04',
            'internal_id' => '2.04',
            'floor' => ClassroomFloor::SECONDO,
            'building' => ClassroomBuilding::SEDE_CENTRALE,
            'type' => ClassroomType::AULA,
        ]);
    }
}
