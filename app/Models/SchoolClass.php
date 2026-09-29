<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; 
use App\Enums\Track;

/**
 * @property string|null $internal_id
 * @property string $description
 * @property int $year
 * @property string $section
 * @property Track|null $track
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['internal_id', 'year', 'section', 'track'])]

class SchoolClass extends Model
{
    protected function casts(): array
    {
        return [
            'track' => Track::class,
        ];
    }
}
