<?php

declare(strict_types=1);

namespace App\Interview\Domain\ValueObjects;

enum Suitability: string
{
    case Yes = 'Ja';
    case Maybe = 'Kanskje';
    case No = 'Nei';
}
