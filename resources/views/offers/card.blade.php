<div class="flex items-center justify-between gap-2">
    <a href="{{ route('offers.show', $item) }}" class="font-mono text-sm font-semibold text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $item->number }}</a>
    <a href="{{ route('offers.pdf', $item) }}" class="text-ink-400 hover:text-ink-900" aria-label="PDF"><x-icon name="download" class="h-4 w-4" /></a>
</div>
<p class="mt-1 truncate text-sm font-medium text-ink-800">{{ $item->company?->name }}</p>
<p class="truncate text-xs text-ink-500">{{ $item->deal?->title }}</p>
<div class="mt-3 flex items-center justify-between gap-2">
    <p class="text-md font-bold text-ink-900 tnum">{{ money($item->total) }}</p>
    <span class="flex h-6 items-center gap-1 rounded-md px-1.5 text-xs text-ink-500 tnum"><x-icon name="calendar" class="h-3.5 w-3.5" />{{ format_date_short($item->issued_at) }}</span>
</div>
