<div class="flex items-center justify-between">
    <a href="{{ route('offers.show', $item) }}" class="font-mono text-sm font-semibold text-slate-900 hover:text-brand-700">{{ $item->number }}</a>
    <a href="{{ route('offers.pdf', $item) }}" class="text-slate-400 hover:text-brand-600" title="PDF"><x-icon name="download" class="h-4 w-4" /></a>
</div>
<p class="mt-0.5 truncate text-xs font-medium text-slate-700">{{ $item->company?->name }}</p>
<p class="truncate text-xs text-slate-500">{{ $item->deal?->title }}</p>
<div class="mt-3 flex items-center justify-between">
    <span class="text-sm font-bold text-slate-900">{{ money($item->total) }}</span>
    <span class="text-xs text-slate-500">{{ format_date($item->issued_at) }}</span>
</div>
