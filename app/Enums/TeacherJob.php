<?php

namespace App\Enums;

enum TeacherJob: string
{
    case TEACHER = 'docente';
    case ITP = 'itp';

    public function label(): string
    {
        return match ($this) {
            self::TEACHER => 'Docente',
            self::ITP => 'ITP',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
