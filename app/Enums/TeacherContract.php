<?php

namespace App\Enums;

enum TeacherContract: string
{
    case FIXED_TERM = 'determinato';
    case PERMANENT = 'indeterminato';

    public function label(): string
    {
        return match ($this) {
            self::FIXED_TERM => 'Determinato',
            self::PERMANENT => 'Indeterminato',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
