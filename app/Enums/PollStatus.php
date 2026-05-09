<?php
namespace App\Enums;

enum PollStatus: string
{
    case OPEN = 'Open';
    case CLOSED = 'Closed';
}
