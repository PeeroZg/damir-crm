@use('App\Enums\OfferStatus')
@php
    $canManage = auth()->user()->can('changeStatus', $offer);
    $status = $offer->status;
    $daysLeft = $offer->valid_until && $status === OfferStatus::Sent && ! $offer->isExpired() ? (int) now()->startOfDay()->diffInDays($offer->valid_until) : null;
@endphp
<x-layouts.app :title="$offer->number">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :back="['url' => route('offers.index'), 'label' => __('Offers')]">
        <x-slot:title>{{ __('Offer') }} <span class="font-mono text-[28px] font-semibold tracking-tight">{{ $offer->number }}</span></x-slot:title>
        <x-slot:subtitle>
            <x-status :value="$status" />
            @if ($offer->company)<a href="{{ route('companies.show', $offer->company) }}" class="inline-flex items-center gap-1.5 hover:text-ink-900"><x-icon name="building" class="h-4 w-4 text-ink-400" />{{ $offer->company->name }}</a>@endif
            @if ($offer->deal)<span class="text-ink-300">·</span><a href="{{ route('deals.show', $offer->deal) }}" class="inline-flex items-center gap-1.5 hover:text-ink-900"><x-icon name="briefcase" class="h-4 w-4 text-ink-400" />{{ $offer->deal->title }}</a>@endif
        </x-slot:subtitle>
        <x-slot:actions>
            <div x-data="{ open: false }" class="relative">
                <x-button variant="secondary" icon="download" @click="open = !open" ::aria-expanded="open.toString()">PDF<x-icon name="chevron-down" class="h-4 w-4" /></x-button>
                <div x-cloak x-show="open" @click.outside="open = false" x-transition class="absolute right-0 z-20 mt-1 w-44 rounded-xl bg-white p-1 shadow-pop ring-1 ring-ink-150">
                    @foreach (config('crm.locales') as $code => $label)
                        <a href="{{ route('offers.pdf', [$offer, 'lang' => $code]) }}" class="flex items-center justify-between rounded-lg px-3 py-2 text-sm text-ink-700 hover:bg-ink-50">{{ $label }}<span class="font-mono text-2xs uppercase text-ink-500">{{ $code }}</span></a>
                    @endforeach
                </div>
            </div>
            @if ($offer->isEditable())
                @can('update', $offer)
                    <x-button variant="secondary" icon="pencil" :href="route('offers.edit', $offer)">{{ __('Edit') }}</x-button>
                @endcan
            @endif
            @can('revise', $offer)
                <form method="POST" action="{{ route('offers.revise', $offer) }}">
                    @csrf
                    <x-button type="submit" variant="secondary" icon="copy">{{ __('New revision') }}</x-button>
                </form>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Status progress --}}
    <section class="overflow-hidden rounded-xl border border-ink-150 bg-white shadow-card" aria-label="{{ __('Offer status') }}">
        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:p-5">
            <ol class="flex items-center gap-2 text-sm" aria-label="{{ __('Status progress') }}">
                <li @class(['flex items-center gap-2', 'font-semibold text-ink-900' => $status === OfferStatus::Draft, 'text-ink-500' => $status !== OfferStatus::Draft]) @if ($status === OfferStatus::Draft) aria-current="step" @endif>
                    <span @class(['flex h-6 w-6 items-center justify-center rounded-full', 'bg-ink-900 text-white' => $status !== OfferStatus::Draft, 'bg-draft-solid text-white' => $status === OfferStatus::Draft])><x-icon :name="$status === OfferStatus::Draft ? 'pencil' : 'check'" class="h-3.5 w-3.5" /></span>{{ __('Draft') }}
                </li>
                <li class="h-px w-6 bg-ink-300" aria-hidden="true"></li>
                <li @class(['flex items-center gap-2', 'font-semibold text-ink-900' => $status === OfferStatus::Sent, 'text-ink-500' => $status !== OfferStatus::Sent]) @if ($status === OfferStatus::Sent) aria-current="step" @endif>
                    @if ($status === OfferStatus::Sent)
                        <span class="relative flex h-6 w-6 items-center justify-center rounded-full bg-sent-solid text-white"><span class="absolute inset-0 animate-ping rounded-full bg-sent-solid/40"></span><x-icon name="send" class="relative h-3.5 w-3.5" /></span>
                    @elseif ($status === OfferStatus::Draft)
                        <span class="flex h-6 w-6 items-center justify-center rounded-full border border-dashed border-ink-300"><x-icon name="send" class="h-3.5 w-3.5" /></span>
                    @else
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-white"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                    @endif
                    {{ __('Sent') }}
                </li>
                <li @class(['h-px w-6', 'border-t border-dashed border-ink-300' => ! $status->isFinal(), 'bg-ink-300' => $status->isFinal()]) aria-hidden="true"></li>
                <li @class(['flex items-center gap-2', 'font-semibold text-ink-900' => $status->isFinal(), 'text-ink-500' => ! $status->isFinal()]) @if ($status->isFinal()) aria-current="step" @endif>
                    @if ($status->isFinal())
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-{{ $status->color() }}-solid text-white"><x-icon :name="match ($status) { OfferStatus::Accepted => 'thumb-up', OfferStatus::Rejected => 'thumb-down', default => 'clock' }" class="h-3.5 w-3.5" /></span>{{ $status->label() }}
                    @else
                        <span class="flex h-6 w-6 items-center justify-center rounded-full border border-dashed border-ink-300"><x-icon name="question" class="h-3.5 w-3.5" /></span>{{ __('Answer') }}
                    @endif
                </li>
            </ol>
            <p class="text-base text-ink-700 sm:ml-4 sm:border-l sm:border-ink-150 sm:pl-5">
                @switch($status)
                    @case(OfferStatus::Draft) {{ __('Ready? Mark the offer as sent once the customer has it.') }} @break
                    @case(OfferStatus::Sent) {{ __('Waiting for the customer. Record the answer when it arrives.') }} @break
                    @case(OfferStatus::Accepted) {{ __('Accepted — the deal is won.') }} @break
                    @case(OfferStatus::Rejected) {{ __('Rejected. Create a new revision to try again.') }} @break
                    @default {{ __('This offer expired before the customer answered.') }}
                @endswitch
            </p>
            @if ($canManage && ! $status->isFinal())
                <div class="flex flex-wrap gap-2 sm:ml-auto">
                    @foreach ($status === OfferStatus::Draft ? [OfferStatus::Sent] : [OfferStatus::Accepted, OfferStatus::Rejected] as $next)
                        <form method="POST" action="{{ route('offers.status', $offer) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $next->value }}">
                            @if ($next === OfferStatus::Sent)
                                <x-button type="submit" icon="send">{{ __('Mark as sent') }}</x-button>
                            @elseif ($next === OfferStatus::Accepted)
                                <x-button type="submit" variant="success" icon="thumb-up">{{ __('Accepted') }}</x-button>
                            @else
                                <x-button type="submit" variant="secondary" icon="thumb-down" class="text-lost-fg hover:border-lost-solid/40 hover:bg-lost-bg">{{ __('Rejected') }}</x-button>
                            @endif
                        </form>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <div class="mt-3 grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="flex min-w-0 flex-col gap-3">
            <section class="card overflow-hidden" aria-label="{{ __('Offer') }}">
                <dl class="grid grid-cols-2 gap-5 border-b border-ink-100 p-5 md:grid-cols-4">
                    <div><dt class="meta-label">{{ __('Issued') }}</dt><dd class="meta-value tnum">{{ format_date($offer->issued_at) }}</dd></div>
                    <div><dt class="meta-label">{{ __('Valid until') }}</dt><dd @class(['meta-value tnum', 'text-lost-fg' => $status === OfferStatus::Sent && $offer->isExpired()])>{{ format_date($offer->valid_until) }}@if ($daysLeft !== null) <span class="text-xs font-normal text-ink-500">· {{ trans_choice('{0} last day|{1} :count day left|[2,*] :count days left', $daysLeft, ['count' => $daysLeft]) }}</span>@endif</dd></div>
                    <div><dt class="meta-label">{{ __('Prepared by') }}</dt><dd class="meta-value">@include('partials.owner', ['user' => $offer->owner])</dd></div>
                    <div><dt class="meta-label">{{ __('Contact') }}</dt><dd class="meta-value">{{ $offer->deal?->contact?->full_name ?? '—' }}</dd></div>
                </dl>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead><tr><th>{{ __('Description') }}</th><th class="text-right">{{ __('Qty') }}</th><th class="text-right">{{ __('Unit price') }}</th><th class="text-right">{{ __('Disc. %') }}</th><th class="text-right">{{ __('Amount') }}</th></tr></thead>
                        <tbody>
                            @foreach ($offer->items as $item)
                                <tr>
                                    <td class="font-medium text-ink-900">{{ $item->description }}</td>
                                    <td class="text-right tnum">{{ App\Support\Money::number($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2) }} <span class="text-ink-500">{{ $item->unit }}</span></td>
                                    <td class="text-right tnum">{{ money($item->unit_price) }}</td>
                                    <td class="text-right tnum">@if ((float) $item->discount_percent){{ App\Support\Money::number($item->discount_percent, 0) }} %@else<span class="text-ink-400">—</span>@endif</td>
                                    <td class="text-right font-semibold text-ink-900 tnum">{{ money($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end border-t border-ink-100 bg-ink-25 p-5">
                    <dl class="w-full max-w-[300px] space-y-2 text-base">
                        <div class="flex justify-between"><dt class="text-ink-500">{{ __('Subtotal') }}</dt><dd class="font-medium tnum">{{ money($offer->subtotal) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-500">{{ __('VAT') }} ({{ App\Support\Money::number($offer->vat_rate, 0) }} %)</dt><dd class="font-medium tnum">{{ money($offer->vat_amount) }}</dd></div>
                        <div class="flex items-baseline justify-between border-t border-ink-200 pt-2.5"><dt class="font-semibold text-ink-900">{{ __('Total') }}</dt><dd class="relative font-display text-2xl font-bold text-ink-900 tnum"><span class="absolute inset-x-[-4px] bottom-[2px] h-[10px] rounded-sm bg-volt-400" aria-hidden="true"></span><span class="relative">{{ money($offer->total) }}</span></dd></div>
                    </dl>
                </div>
            </section>

            @if ($offer->notes)
                <section class="card"><div class="card-header"><h2 class="card-title">{{ __('Notes & terms') }}</h2></div><p class="whitespace-pre-line p-5 text-base text-ink-800">{{ $offer->notes }}</p></section>
            @endif
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('offers.pdf', $offer) }}" class="group card block overflow-hidden p-4 transition hover:border-ink-300" aria-label="{{ __('Download PDF') }}">
                <div class="relative mx-auto aspect-[210/297] w-full max-w-[200px] rounded-md bg-white p-3 shadow-pop ring-1 ring-ink-150 transition group-hover:-translate-y-0.5" aria-hidden="true">
                    <div class="flex items-center justify-between"><span class="h-3 w-3 rounded-[3px] bg-ink-900"></span><span class="font-display text-[9px] font-bold uppercase">{{ __('Offer') }}</span></div>
                    <div class="mt-3 h-1 w-14 rounded bg-ink-200"></div><div class="mt-1 h-1 w-10 rounded bg-ink-150"></div>
                    <div class="mt-4 h-2 rounded-sm bg-ink-900"></div>
                    @foreach ($offer->items->take(5) as $item)
                        <div class="mt-1.5 flex justify-between"><div class="h-1 w-16 rounded bg-ink-150"></div><div class="h-1 w-6 rounded bg-ink-150"></div></div>
                    @endforeach
                    <div class="ml-auto mt-4 h-2 w-14 rounded-sm bg-volt-400"></div>
                </div>
                <p class="mt-3 flex items-center justify-center gap-1.5 text-sm font-medium text-ink-700 group-hover:text-ink-900"><x-icon name="download" class="h-4 w-4" />{{ $offer->number }}.pdf</p>
            </a>

            @if ($offer->parent || $offer->revisions->isNotEmpty())
                <section class="card">
                    <div class="card-header"><h2 class="card-title">{{ __('Revisions') }}</h2></div>
                    <ul class="divide-y divide-ink-100">
                        @foreach (collect([$offer->parent])->filter()->merge($offer->revisions)->sortBy('revision') as $rev)
                            <li class="flex items-center justify-between px-5 py-3"><a href="{{ route('offers.show', $rev) }}" class="link font-mono text-sm">{{ $rev->number }}</a><x-status :value="$rev->status" /></li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @include('partials.history', ['logs' => $offer->auditLogs])
            @can('delete', $offer)
                <div class="flex justify-end"><x-delete-button :action="route('offers.destroy', $offer)" :label="__('Delete offer')" /></div>
            @endcan
        </div>
    </div>
</div>
</x-layouts.app>
