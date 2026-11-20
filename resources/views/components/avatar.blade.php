@props(['name' => '?', 'size' => 'md'])
@php
    $initials = collect(preg_split('/\s+/', trim($name)) ?: [])->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
    $palette = ['sky', 'green', 'indigo', 'violet', 'amber', 'rose'];
    $color = $palette[crc32($name) % count($palette)];
    $sizes = ['sm' => 'avatar-sm', 'md' => '', 'lg' => 'avatar-lg'];
@endphp
<span {{ $attributes->merge(['class' => trim('avatar avatar-'.$color.' '.$sizes[$size])]) }} title="{{ $name }}">{{ mb_strtoupper($initials ?: '?') }}</span>
