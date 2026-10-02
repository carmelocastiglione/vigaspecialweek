<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Category;
use App\Models\EnrichmentActivity;
use App\Models\RemedialActivity;
use App\Models\Subject;
use App\Models\User;

it('links remedial activities to their base activity and subject', function () {
    $remedialActivity = RemedialActivity::factory()->create();
    $activity = $remedialActivity->activity;

    expect($activity)->toBeInstanceOf(Activity::class);
    expect($activity->activity_type)->toBe(ActivityType::REMEDIAL);
    expect($activity->remedialActivity->is($remedialActivity))->toBeTrue();
    expect($remedialActivity->subject)->toBeInstanceOf(Subject::class);
    expect($remedialActivity->year)->toBeInt();
});

it('links enrichment activities to their base activity and category', function () {
    $enrichmentActivity = EnrichmentActivity::factory()->create();
    $activity = $enrichmentActivity->activity;

    expect($activity)->toBeInstanceOf(Activity::class);
    expect($activity->activity_type)->toBe(ActivityType::ENRICHMENT);
    expect($activity->enrichmentActivity->is($enrichmentActivity))->toBeTrue();
    expect($enrichmentActivity->category)->toBeInstanceOf(Category::class);
    expect($enrichmentActivity->external)->toBeFalse();
});

it('resolves the primary and secondary users separately', function () {
    $user = User::factory()->create();
    $secondaryUser = User::factory()->create();
    $activity = Activity::factory()->create([
        'user_id' => $user->id,
        'secondary_user_id' => $secondaryUser->id,
    ]);

    expect($activity->user->is($user))->toBeTrue();
    expect($activity->secondaryUser->is($secondaryUser))->toBeTrue();
});
