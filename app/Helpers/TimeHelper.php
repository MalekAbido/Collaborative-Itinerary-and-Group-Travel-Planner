<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;
use Exception;

class TimeHelper
{
    public static function convertToUTC(string $localDatetime, string $clientTimezone = 'UTC')
    {
        $format = 'Y-m-d H:i:s';
        try {
            $clientTz = new DateTimeZone($clientTimezone);
            $utcTz = new DateTimeZone('UTC');

            $dt = new DateTime($localDatetime, $clientTz);
            $dt->setTimezone($utcTz);

            return $dt->format($format);
        } catch (Exception $e) {
            // Log the error if necessary
            error_log("Timezone Conversion Error: " . $e->getMessage());
            return null;
        }
    }

    public static function formatLocal(string $utcDatetime, string $targetTimezone = 'Africa/Cairo')
    {
        $format = 'M j, g:i A';
        if (!$utcDatetime) return '';
        try {
            $utcTz = new DateTimeZone('UTC');
            $displayTz = new DateTimeZone($targetTimezone);

            $dt = new DateTime($utcDatetime, $utcTz);
            $dt->setTimezone($displayTz);

            return $dt->format($format);
        } catch (Exception $e) {
            return $utcDatetime; // Fallback
        }
    }
}
