<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case  PLANNED = 'planned';

    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
