<div class="flex items-start justify-between gap-2">
    <a href="{{ route('deals.show', $item) }}" class="text-sm font-semibold leading-snug text-slate-900 hover:text-brand-700">{{ $item->title }}</a>
    @if ($item->owner)<x-avatar :name="$item->owner->name" size="sm" />@endif
</div>
<p class="mt-0.5 truncate text-xs text-slate-500">{{ $item->company?->name }}</p>
<div class="mt-3 flex items-center justify-between">
    <span class="text-sm font-bold text-slate-900">{{ money($item->value) }}</span>
    @if ($item->isOpen() && $item->expected_close_date)
        <span @class(['inline-flex items-center gap-1 text-xs', 'font-medium text-rose-600' => $item->expected_close_date->isPast(), 'text-slate-500' => ! $item->expected_close_date->isPast()])>
            <x-icon name="calendar" class="h-3.5 w-3.5" /> {{ format_date($item->expected_close_date) }}
        </span>
    @elseif ($item->lost_reason)
        <span class="truncate text-xs italic text-slate-500" title="{{ $item->lost_reason }}">{{ Str::limit($item->lost_reason, 26) }}</span>
    @endif
</div>
@if ($item->isOpen() && auth()->user()->can('create', [App\Models\Offer::class, $item]))
    <a href="{{ route('offers.create', ['deal_id' => $item->id]) }}" class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-brand-600 hover:text-brand-700">
        <x-icon name="plus" class="h-3.5 w-3.5" /> {{ __('Offer') }}
    </a>
@endif
