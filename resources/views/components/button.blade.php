@props(['variant' => 'primary', 'href' => null, 'icon' => null, 'iconRight' => null, 'size' => 'md'])
@php
    // Literal class names so Tailwind's content scanner keeps them in the build.
    $variants = [
        'primary' => 'btn-primary', 'secondary' => 'btn-secondary', 'ghost' => 'btn-ghost',
        'danger' => 'btn-danger', 'success' => 'btn-success', 'accent' => 'btn-accent',
    ];
    $sizes = ['sm' => 'btn-sm', 'md' => '', 'lg' => 'btn-lg'];
    $classes = trim('btn '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? ''));
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>@if ($icon)<x-icon :name="$icon" />@endif{{ $slot }}@if ($iconRight)<x-icon :name="$iconRight" />@endif</a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>@if ($icon)<x-icon :name="$icon" />@endif{{ $slot }}@if ($iconRight)<x-icon :name="$iconRight" />@endif</button>
@endif
