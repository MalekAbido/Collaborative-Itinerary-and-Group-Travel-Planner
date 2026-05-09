<?php
namespace App\Enums;

enum ActivityStatus: string
{
    case DRAFT = 'Draft';
    case PROPOSED = 'Proposed';
    case REJECTED = 'Rejected';
    case CONFIRMED = 'Confirmed';
    case DECLINED = 'Declined';
    case REMOVED = 'Removed';
}
