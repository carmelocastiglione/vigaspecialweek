<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;
        
        return [
            'internal_id' => 'Mat' . $counter,
            'description' => 'Materia ' . $counter,
            'department_id' => Department::inRandomOrder()->first()?->id
        ];

    }
}
