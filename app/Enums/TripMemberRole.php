<?php
namespace App\Enums;

enum TripMemberRole: string
{
    case MEMBER = 'Member';
    case EDITOR = 'Editor';
    case ORGANIZER = 'Organizer';
}