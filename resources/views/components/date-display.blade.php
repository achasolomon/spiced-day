@props([
    'date' => null,
    'format' => null,
    'tz' => null,
    'fallback' => null,
])

@php
use Carbon\Carbon;

// Determine fallback text
$fallback = $fallback ?? 'N/A';

if (empty($date)) {
    echo $fallback;
    return;
}

// If it's already a Carbon instance, use it; otherwise try to parse
try {
    $dt = $date instanceof Carbon ? $date : Carbon::parse($date);
} catch (\Throwable $e) {
    echo $fallback;
    return;
}

// Apply timezone if provided or use app timezone
$timezone = $tz ?? config('app.timezone');
if ($timezone) {
    $dt = $dt->setTimezone($timezone);
}

$format = $format ?? 'M d, Y \a\t g:i A';
echo $dt->format($format);
@endphp
