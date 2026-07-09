@props([
    'date' => null,
    'format' => 'M d, Y \a\t g:i A',
    'user' => null,
    'fallback' => 'N/A',
])

@php
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

if (empty($date)) {
    echo $fallback;
    return;
}

try {
    $dt = $date instanceof Carbon ? $date : Carbon::parse($date);
} catch (\Throwable $e) {
    echo $fallback;
    return;
}

// Use provided user, fallback to authenticated user, then UTC
$userForTz = $user ?? auth()->user();
$timezone = $userForTz?->timezone ?? 'UTC';

// Convert to user's timezone
$dt = $dt->setTimezone($timezone);
echo $dt->format($format);
@endphp
