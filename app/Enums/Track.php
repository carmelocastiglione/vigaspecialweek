<?php
    /**
     * Track enum values:
     * - itt: biennio tecnologico
     * - itc: biennio economico
     * - tecnologico: triennio tecnologico
     * - economico: triennio economico
     * - tutti gli altri (turistico, afm, rim, informatica, grafica, elettronica, meccatronica): indirizzi del triennio
     */
    
namespace App\Enums;

enum Track: string
{
    case TURISTICO = 'turistico';
    case AFM = 'afm';
    case RIM = 'rim';
    case INFORMATICA = 'informatica';
    case GRAFICA = 'grafica';
    case ELETTRONICA = 'elettronica';
    case MECCATRONICA = 'meccatronica';
    case ITC = 'itc';
    case ITT = 'itt';
    case TECNOLOGICO = 'tecnologico';
    case ECONOMICO = 'economico';

    public function label(): string
    {
        return match($this) {
            self::TURISTICO => 'Turistico',
            self::AFM => 'AFM',
            self::RIM => 'RIM',
            self::INFORMATICA => 'Informatica',
            self::GRAFICA => 'Grafica',
            self::ELETTRONICA => 'Elettronica',
            self::MECCATRONICA => 'Meccatronica',
            self::ITC => 'ITC',
            self::ITT => 'ITT',
            self::TECNOLOGICO => 'Tecnologico',
            self::ECONOMICO => 'Economico',
        };
    }

    public static function values(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
