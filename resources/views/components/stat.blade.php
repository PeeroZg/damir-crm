@props(['label', 'value', 'hint' => null, 'icon' => 'chart', 'tone' => 'neutral'])
@php
    $tones = [
        'neutral' => 'bg-ink-50 text-ink-600 ring-1 ring-inset ring-ink-150',
        'won' => 'bg-won-bg text-won-fg',
        'lead' => 'bg-lead-bg text-lead-fg',
        'volt' => 'bg-volt-400 text-ink-900',
    ];
@endphp
<div class="card p-4 sm:p-5">
    <div class="flex items-center justify-between">
        <p class="text-sm font-medium text-ink-600">{{ $label }}</p>
        <span class="flex h-7 w-7 items-center justify-center rounded-md {{ $tones[$tone] ?? $tones['neutral'] }}"><x-icon :name="$icon" class="h-4 w-4" /></span>
    </div>
    <p class="mt-3 font-display text-[26px] font-bold leading-none tracking-tight text-ink-900 tnum">{{ $value }}</p>
    @if ($hint)
        <p class="mt-2 text-xs text-ink-500">{{ $hint }}</p>
    @endif
</div>
