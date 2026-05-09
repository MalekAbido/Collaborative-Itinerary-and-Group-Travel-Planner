<?php
namespace App\Enums;

enum RatingOption: string
{
    case MUST_HAVE = 'Must Have';
    case NICE_TO_HAVE = 'Nice to Have';
    case NOT_NEEDED = 'Not Needed';
}