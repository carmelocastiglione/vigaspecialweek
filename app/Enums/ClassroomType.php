<?php

namespace App\Enums;

enum ClassroomType: string
{
    case AULA = 'aula';
    case LAB_INFORMATICA = 'laboratorio informatica';
    case AULA_MAGNA = 'aula magna';
    case PALESTRA = 'palestra';
    case LAB_SCIENZE = 'laboratorio scienze';
    case ESTERNO = 'esterno';
    case BIBLIOTECA = 'biblioteca';
    case ALTRO = 'altro';

    public function label(): string
    {
        return match($this) {
            self::AULA => 'Aula',
            self::LAB_INFORMATICA => 'Laboratorio Informatica',
            self::AULA_MAGNA => 'Aula Magna',
            self::PALESTRA => 'Palestra',
            self::LAB_SCIENZE => 'Laboratorio Scienze',
            self::ESTERNO => 'Esterno',
            self::BIBLIOTECA => 'Biblioteca',
            self::ALTRO => 'Altro',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
