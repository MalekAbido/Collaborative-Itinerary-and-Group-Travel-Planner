<?php
namespace App\Enums;

enum TripRole: string
{
    case MEMBER = 'Member';
    case EDITOR = 'Editor';
    case ORGANIZER = 'Organizer';
}
