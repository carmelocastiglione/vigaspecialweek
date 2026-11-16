<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(0, 124);
        $year = intdiv($number, 25) + 1;
        $section = chr(65 + $number % 25);

        return [
            'description' => $year.$section,
            'year' => $year,
            'section' => $section,
        ];
    }
}
