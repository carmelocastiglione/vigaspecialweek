<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\RemedialActivity;
use Illuminate\Database\Seeder;

class RemedialActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RemedialActivity::factory()
            ->count(10)
            ->state([
                'activity_id' => Activity::factory()->state([
                    'activity_type' => ActivityType::REMEDIAL,
                    'user_id' => 1,
                ]),
                'subject_id' => 1,
            ])
            ->create();
    }
}
