<!-- resources/views/components/ui/progress-bar.blade.php -->
@props(['percentage', 'color' => 'blue', 'size' => 'md', 'showLabel' => true])

@php
$colorClasses = [
    'blue' => 'bg-blue-500',
    'green' => 'bg-green-500',
    'yellow' => 'bg-yellow-500',
    'red' => 'bg-red-500',
    'purple' => 'bg-purple-500',
    'rainbow' => 'rainbow-gradient',
];

$sizeClasses = [
    'sm' => 'h-2',
    'md' => 'h-2.5',
    'lg' => 'h-3',
    'xl' => 'h-4',
];

$colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div class="w-full bg-gray-200 rounded-full {{ $sizeClass }} dark:bg-gray-700">
        <div class="{{ $colorClass }} {{ $sizeClass }} rounded-full transition-all duration-500 ease-out" 
             style="width: {{ $percentage }}%"></div>
    </div>
    @if($showLabel)
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-1">
            <span>Progress</span>
            <span>{{ $percentage }}%</span>
        </div>
    @endif
</div>
