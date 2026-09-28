<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|null $internal_id
 * @property string $description
 * @property Department|null $department
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

}
