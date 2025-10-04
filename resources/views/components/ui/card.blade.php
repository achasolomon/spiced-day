<!-- resources/views/components/ui/card.blade.php -->
@props(['padding' => 'p-6', 'hover' => false])

@php
$classes = "glass-card rounded-2xl {$padding}";
if ($hover) {
    $classes .= ' hover-lift';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>