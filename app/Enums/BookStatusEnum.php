<?php

namespace App\Enums;

enum BookStatusEnum: int
{
    case AVAILABLE = 0;    
    case UNAVAILABLE = 1;   

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'available',
            self::UNAVAILABLE => 'unavailable',
        };
    }

    public static function options(): array
    {
        return [
            self::AVAILABLE->value => 'available',
            self::UNAVAILABLE->value => 'unavailable',
        ];
    }
}