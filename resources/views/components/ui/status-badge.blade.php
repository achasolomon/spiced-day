<!-- resources/views/components/ui/status-badge.blade.php -->
@props(['status', 'size' => 'sm'])

@php
$classes = [
    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'under-review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
];

$sizeClasses = [
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-2.5 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-2 text-base',
];

$statusClass = $classes[$status] ?? $classes['draft'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-medium {$statusClass} {$sizeClass}"]) }}>
    {{ ucwords(str_replace(['-', '_'], ' ', $status)) }}
</span>