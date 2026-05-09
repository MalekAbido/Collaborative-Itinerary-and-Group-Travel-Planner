<?php
namespace App\Enums;

enum RatingOption: string
{
    case MUST_HAVE = 'MUST_HAVE';
    case NICE_TO_HAVE = 'NICE_TO_HAVE';
    case NOT_NEEDED = 'NOT_NEEDED';

    public function label(): string
    {
        return match($this) {
            self::MUST_HAVE => 'Must Have',
            self::NICE_TO_HAVE => 'Nice to Have',
            self::NOT_NEEDED => 'Not Needed',
        };
    }
}