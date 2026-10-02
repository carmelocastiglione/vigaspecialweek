<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\RemedialActivity;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RemedialActivity>
 */
class RemedialActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory()->state([
                'activity_type' => ActivityType::REMEDIAL,
            ]),
            'subject_id' => Subject::factory(),
            'year' => fake()->numberBetween(1, 5),
        ];
    }
}
