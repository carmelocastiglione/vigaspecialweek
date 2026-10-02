<?php

namespace App\Enums;

enum TrackGroup: string
{
    case BIENNIO = 'biennio';
    case TRIENNIO = 'triennio';
    case ECONOMICO = 'economico';
    case TECNOLOGICO = 'tecnologico';
    case TUTTI = 'tutti';

    public function label(): string
    {
        return match ($this) {
            self::BIENNIO => 'Biennio',
            self::TRIENNIO => 'Triennio',
            self::ECONOMICO => 'Economico',
            self::TECNOLOGICO => 'Tecnologico',
            self::TUTTI => 'Tutti',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
