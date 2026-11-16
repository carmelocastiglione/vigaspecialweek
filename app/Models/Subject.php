<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string|null $internal_id
 * @property string $description
 * @property Department|null $department
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['internal_id', 'description', 'department_id'])]

class Subject extends Model
{
    use HasFactory;

    /**
     * Get the department that owns the subject.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /** @return HasMany<TeachingAssignment, $this> */
    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }
}
