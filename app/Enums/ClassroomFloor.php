<?php

namespace App\Enums;

enum ClassroomFloor: string
{
    case TERRA = 'terra';
    case PRIMO = 'primo';
    case SECONDO = 'secondo';
    case TERZO = 'terzo';
    case SEMINTERRATO = 'seminterrato';

    public function label(): string
    {
        return match($this) {
            self::TERRA => 'Piano Terra',
            self::PRIMO => 'Primo Piano',
            self::SECONDO => 'Secondo Piano',
            self::TERZO => 'Terzo Piano',
            self::SEMINTERRATO => 'Seminterrato',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
