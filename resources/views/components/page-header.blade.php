@props(['title', 'subtitle' => null, 'back' => null])
@if ($back)
    <a href="{{ $back['url'] }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-500 hover:text-ink-900">
        <x-icon name="arrow-left" class="h-4 w-4" />{{ $back['label'] }}
    </a>
@endif
<div @class(['flex flex-wrap justify-between gap-4', 'items-start' => $subtitle, 'items-end' => ! $subtitle, 'mt-2' => $back, 'mb-6'])>
    <div class="min-w-0">
        {{ $eyebrow ?? '' }}
        <h1 class="page-title">{{ $title }}</h1>
        @if ($subtitle)
            <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-base text-ink-600">{{ $subtitle }}</div>
        @endif
        @isset($description)
            <p class="page-sub">{{ $description }}</p>
        @endisset
    </div>
    @isset($actions)
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
