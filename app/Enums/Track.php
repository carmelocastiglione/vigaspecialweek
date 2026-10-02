<?php

/**
 * Valori originali:
 * - itt: biennio tecnologico
 * - itc: biennio economico
 * - tecnologico: triennio tecnologico
 * - economico: triennio economico
 * - tutti gli altri (turistico, afm, rim, informatica, grafica, elettronica, meccatronica): indirizzi del triennio
 */

namespace App\Enums;

enum Track: string
{
    case AFM = 'afm';
    case RIM = 'rim';
    case TURISTICO = 'turistico';
    case INFORMATICA = 'informatica';
    case GRAFICA = 'grafica';
    case ELETTRONICA = 'elettronica';
    case MECCATRONICA = 'meccatronica';

    public function label(): string
    {
        return match ($this) {
            self::AFM => 'AFM',
            self::RIM => 'RIM',
            self::TURISTICO => 'Turistico',
            self::INFORMATICA => 'Informatica',
            self::GRAFICA => 'Grafica',
            self::ELETTRONICA => 'Elettronica',
            self::MECCATRONICA => 'Meccatronica',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
