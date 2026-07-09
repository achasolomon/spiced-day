<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TimezoneHelper
{
    /**
     * Format a datetime for a specific user's timezone
     * 
     * @param Carbon|string|null $dateTime
     * @param \App\Models\User|null $user
     * @param string $format
     * @return string
     */
    public static function formatForUser($dateTime, $user = null, $format = 'M d, Y \a\t g:i A')
    {
        if (!$dateTime) {
            return 'N/A';
        }

        try {
            $date = $dateTime instanceof Carbon ? $dateTime : Carbon::parse($dateTime);
        } catch (\Throwable $e) {
            return 'N/A';
        }

        // Get user's timezone, default to UTC if not available
        $timezone = $user?->timezone ?? auth()->user()?->timezone ?? 'UTC';

        return $date->setTimezone($timezone)->format($format);
    }

    /**
     * Format a datetime range (e.g., start and end times)
     * 
     * @param Carbon|string|null $startDateTime
     * @param Carbon|string|null $endDateTime
     * @param \App\Models\User|null $user
     * @return string
     */
    public static function formatTimeRange($startDateTime, $endDateTime, $user = null)
    {
        if (!$startDateTime || !$endDateTime) {
            return 'N/A';
        }

        try {
            $start = $startDateTime instanceof Carbon ? $startDateTime : Carbon::parse($startDateTime);
            $end = $endDateTime instanceof Carbon ? $endDateTime : Carbon::parse($endDateTime);
        } catch (\Throwable $e) {
            return 'N/A';
        }

        $timezone = $user?->timezone ?? auth()->user()?->timezone ?? 'UTC';

        return $start->setTimezone($timezone)->format('g:i A') . ' - ' . $end->setTimezone($timezone)->format('g:i A');
    }

    /**
     * Get all available timezones
     * 
     * @return array
     */
    public static function getAvailableTimezones()
    {
        return [
            'UTC' => 'UTC',
            'America/Toronto' => 'America/Toronto (Eastern Time)',
            'America/New_York' => 'America/New_York (Eastern Time)',
            'America/Chicago' => 'America/Chicago (Central Time)',
            'America/Denver' => 'America/Denver (Mountain Time)',
            'America/Los_Angeles' => 'America/Los_Angeles (Pacific Time)',
            'Europe/London' => 'Europe/London (GMT)',
            'Europe/Paris' => 'Europe/Paris (CET)',
            'Africa/Lagos' => 'Africa/Lagos (WAT - Nigeria)',
            'Africa/Cairo' => 'Africa/Cairo (EET)',
            'Asia/Kolkata' => 'Asia/Kolkata (IST)',
            'Asia/Bangkok' => 'Asia/Bangkok (ICT)',
            'Asia/Shanghai' => 'Asia/Shanghai (CST)',
            'Asia/Tokyo' => 'Asia/Tokyo (JST)',
            'Australia/Sydney' => 'Australia/Sydney (AEDT)',
            'Pacific/Auckland' => 'Pacific/Auckland (NZDT)',
        ];
    }
}
