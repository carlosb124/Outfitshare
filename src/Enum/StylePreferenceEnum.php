<?php

namespace App\Enum;

enum StylePreferenceEnum: string
{
    case MEN = 'Men';
    case WOMEN = 'Women';
    case UNISEX = 'Unisex';
    case OTHER = 'Other';
}
