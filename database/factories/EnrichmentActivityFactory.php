<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Category;
use App\Models\EnrichmentActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EnrichmentActivity>
 */
class EnrichmentActivityFactory extends Factory
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
                'activity_type' => ActivityType::ENRICHMENT,
            ]),
            'category_id' => Category::factory(),
            'external' => false,
        ];
    }
}
