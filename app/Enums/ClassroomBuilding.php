<?php

namespace App\Enums;

enum ClassroomBuilding: string
{
    case PALAZZINA = 'palazzina';
    case OFFICINA = 'officina';
    case SEDE_CENTRALE = 'sede centrale';
    case PALESTRA = 'palestra';

    public function label(): string
    {
        return match($this) {
            self::PALAZZINA => 'Palazzina',
            self::OFFICINA => 'Officina',
            self::SEDE_CENTRALE => 'Sede Centrale',
            self::PALESTRA => 'Palestra',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
