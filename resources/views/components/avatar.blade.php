@props(['name' => '?', 'size' => 'md'])
@php
    $initials = collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
    $palette = ['bg-sky-100 text-sky-700', 'bg-violet-100 text-violet-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700', 'bg-indigo-100 text-indigo-700'];
    $color = $palette[crc32($name) % count($palette)];
    $sizes = ['sm' => 'h-6 w-6 text-[10px]', 'md' => 'h-8 w-8 text-xs', 'lg' => 'h-11 w-11 text-sm'];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-semibold '.$color.' '.$sizes[$size]]) }} title="{{ $name }}">
    {{ mb_strtoupper($initials ?: '?') }}
</span>
