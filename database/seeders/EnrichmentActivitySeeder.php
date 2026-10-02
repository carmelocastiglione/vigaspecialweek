<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\EnrichmentActivity;
use Illuminate\Database\Seeder;

class EnrichmentActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EnrichmentActivity::factory()
            ->count(10)
            ->state([
                'activity_id' => Activity::factory()->state([
                    'activity_type' => ActivityType::ENRICHMENT,
                    'user_id' => 1,
                ]),
                'category_id' => 1,
            ])
            ->create();
    }
}
