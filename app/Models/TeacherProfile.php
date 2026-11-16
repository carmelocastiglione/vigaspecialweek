<?php

namespace App\Models;

use App\Enums\TeacherContract;
use App\Enums\TeacherJob;
use Database\Factories\TeacherProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property TeacherJob|null $job
 * @property TeacherContract|null $contract
 */
#[Fillable(['department_id', 'coordinator_class_id', 'responsible_department_id', 'internal_id', 'job', 'contract', 'no_first_hour', 'hours'])]
class TeacherProfile extends Model
{
    /** @use HasFactory<TeacherProfileFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'job' => TeacherJob::class,
            'contract' => TeacherContract::class,
            'no_first_hour' => 'boolean',
            'hours' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return BelongsTo<SchoolClass, $this> */
    public function coordinatorClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'coordinator_class_id');
    }

    /** @return BelongsTo<Department, $this> */
    public function responsibleDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'responsible_department_id');
    }

    /** @return HasMany<TeachingAssignment, $this> */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
