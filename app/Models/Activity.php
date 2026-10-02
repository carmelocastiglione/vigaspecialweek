<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Enums\ClassroomType;
use App\Enums\Track;
use App\Enums\TrackGroup;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $internal_id
 * @property int|null $user_id
 * @property int|null $secondary_user_id
 * @property ActivityType $activity_type
 * @property ClassroomType $classroom_type
 * @property int $max_students
 * @property int $duration
 * @property int|null $repetitions
 * @property Track|null $track
 * @property TrackGroup|null $track_group
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'title',
    'description',
    'internal_id',
    'user_id',
    'secondary_user_id',
    'activity_type',
    'classroom_type',
    'max_students',
    'duration',
    'repetitions',
    'track',
    'track_group',
    'note',
])]
class Activity extends Model
{
    /** @use HasFactory<ActivityFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activity_type' => ActivityType::class,
            'classroom_type' => ClassroomType::class,
            'max_students' => 'integer',
            'duration' => 'integer',
            'repetitions' => 'integer',
            'track' => Track::class,
            'track_group' => TrackGroup::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function secondaryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secondary_user_id');
    }

    public function remedialActivity(): HasOne
    {
        return $this->hasOne(RemedialActivity::class);
    }

    public function enrichmentActivity(): HasOne
    {
        return $this->hasOne(EnrichmentActivity::class);
    }
}
