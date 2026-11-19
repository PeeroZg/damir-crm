@props(['variant' => 'primary', 'href' => null, 'icon' => null, 'size' => 'md'])
@php
    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-500 focus-visible:outline-brand-600',
        'secondary' => 'bg-white text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50',
        'danger' => 'bg-rose-600 text-white shadow-sm hover:bg-rose-500 focus-visible:outline-rose-600',
        'ghost' => 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
        'success' => 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline-emerald-600',
    ];
    $sizes = ['sm' => 'px-2.5 py-1.5 text-xs gap-1', 'md' => 'px-3.5 py-2 text-sm gap-1.5'];
    $classes = 'inline-flex items-center justify-center rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '.$sizes[$size];
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="h-4 w-4" />@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="h-4 w-4" />@endif
        {{ $slot }}
    </button>
@endif
