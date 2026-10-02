<?php

namespace App\Enums;

enum ActivityType: string
{
    case ENRICHMENT = 'potenziamento';
    case REMEDIAL = 'recupero';

    public function label(): string
    {
        return match ($this) {
            self::ENRICHMENT => 'Potenziamento',
            self::REMEDIAL => 'Recupero',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
