<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; 
use App\Enums\ClassroomFloor;
use App\Enums\ClassroomBuilding;
use App\Enums\ClassroomType;

/**
 * @property string|null $internal_id
 * @property string $description
 * @property int $capacity
 * @property ClassroomFloor $floor
 * @property ClassroomBuilding $building
 * @property ClassroomType $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['internal_id', 'description', 'capacity', 'floor', 'building', 'type'])]

class Classroom extends Model  
{
    protected function casts(): array
    {
        return [
            'floor' => ClassroomFloor::class,
            'building' => ClassroomBuilding::class,
            'type' => ClassroomType::class,
        ];
    }
}
