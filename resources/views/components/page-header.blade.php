@props(['title', 'subtitle' => null, 'back' => null])
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
        @if ($back)
            <a href="{{ $back['url'] }}" class="mb-1 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700">
                <span aria-hidden="true">←</span> {{ $back['label'] }}
            </a>
        @endif
        <h1 class="truncate text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-500">{{ $subtitle }}</div>
        @endif
        {{ $meta ?? '' }}
    </div>
    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
