@php
    $late = $item->isOpen() && $item->expected_close_date?->isPast() && ! $item->expected_close_date->isToday();
@endphp
<div class="flex items-start gap-2">
    <a href="{{ route('deals.show', $item) }}" class="line-clamp-2 flex-1 text-base font-semibold leading-5 text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $item->title }}</a>
    @if ($item->owner)<x-avatar :name="$item->owner->name" size="sm" class="ring-0" />@endif
</div>
<p class="mt-1 truncate text-xs text-ink-500">{{ $item->company?->name }}</p>
@if ($item->stage === App\Enums\DealStage::Lost && $item->lost_reason)
    <p class="mt-2 rounded-md bg-lost-bg/60 px-2 py-1.5 text-xs text-lost-fg">“{{ $item->lost_reason }}”</p>
@endif
<div class="mt-3 flex items-center justify-between gap-2">
    <p class="text-md font-bold text-ink-900 tnum">{{ money($item->value) }}</p>
    @if ($item->stage === App\Enums\DealStage::Won && $item->closed_at)
        <span class="flex h-6 items-center gap-1 rounded-md bg-volt-300 px-1.5 text-xs font-medium text-ink-900 tnum"><x-icon name="trophy" class="h-3.5 w-3.5" />{{ format_date_short($item->closed_at) }}</span>
    @elseif ($item->stage === App\Enums\DealStage::Lost && $item->closed_at)
        <span class="flex h-6 items-center gap-1 rounded-md px-1.5 text-xs text-ink-500 tnum"><x-icon name="calendar" class="h-3.5 w-3.5" />{{ format_date_short($item->closed_at) }}</span>
    @elseif ($item->expected_close_date)
        <span @class(['flex h-6 items-center gap-1 rounded-md px-1.5 text-xs tnum', 'bg-lost-bg font-medium text-lost-fg' => $late, 'text-ink-500' => ! $late]) @if ($late) title="{{ __('Expected close date has passed') }}" @endif>
            <x-icon :name="$late ? 'clock' : 'calendar'" class="h-3.5 w-3.5" />{{ format_date_short($item->expected_close_date) }}
        </span>
    @endif
</div>
@if ($item->isOpen() && auth()->user()->can('create', [App\Models\Offer::class, $item]))
    <div class="mt-2.5 flex items-center justify-between border-t border-dashed border-ink-150 pt-2">
        <a href="{{ route('offers.create', ['deal_id' => $item->id]) }}" class="inline-flex items-center gap-1 rounded text-xs font-medium text-ink-600 hover:text-ink-900"><x-icon name="document-plus" class="h-3.5 w-3.5" />{{ __('Offer') }}</a>
        <span class="text-ink-300 opacity-0 transition group-hover:opacity-100" aria-hidden="true"><x-icon name="dots" class="h-4 w-4" /></span>
    </div>
@endif
