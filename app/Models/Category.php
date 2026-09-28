<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $internal_id
 * @property string $description
 */
#[Fillable(['internal_id', 'description'])]

class Category extends Model  
{
    use HasFactory;

}
