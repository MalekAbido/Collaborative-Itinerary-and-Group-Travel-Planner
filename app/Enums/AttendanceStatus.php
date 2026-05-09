<?php
namespace App\Enums;

enum AttendanceStatus: string
{
case PENDING = 'Pending';
case GOING = 'Going';
case NOT_GOING = 'Not Going';
}
