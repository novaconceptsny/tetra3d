<?php

namespace App\Enums\Spot;

enum XmlStatus: string
{
    case PRESENT = 'Present';
    case NOT_PRESENT = 'Not Present';
    case INVALID = 'Invalid';

    public function color(): string
    {
        return match ($this) {
            self::PRESENT => 'success',
            self::NOT_PRESENT => 'danger',
            self::INVALID => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PRESENT => 'fad fa-check-circle',
            self::NOT_PRESENT => 'fad fa-times-circle',
            self::INVALID => 'fad fa-exclamation-circle',
        };
    }
}
