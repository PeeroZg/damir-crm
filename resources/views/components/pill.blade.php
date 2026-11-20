{{-- Status pill. Tones: lead qualified proposal negotiation won lost draft sent expired volt neutral --}}
@props(['tone' => 'neutral', 'icon' => null])
@php
    $icon ??= match ($tone) { 'won' => 'check', 'lost' => 'x', default => null };
@endphp
<span {{ $attributes->merge(['class' => 'pill pill-'.$tone]) }}>
    @if ($icon)<x-icon :name="$icon" class="h-3.5 w-3.5" />@else<span class="pill-dot"></span>@endif{{ $slot }}
</span>
