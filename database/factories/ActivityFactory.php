<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Enums\ClassroomType;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'internal_id' => fake()->unique()->bothify('ACT-####'),
            'user_id' => null,
            'secondary_user_id' => null,
            'activity_type' => ActivityType::ENRICHMENT,
            'classroom_type' => ClassroomType::AULA,
            'max_students' => 20,
            'duration' => 2,
            'repetitions' => null,
            'track' => null,
            'track_group' => null,
            'note' => null,
        ];
    }
}
