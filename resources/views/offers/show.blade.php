@use('App\Enums\OfferStatus')
@php $canManage = auth()->user()->can('changeStatus', $offer); @endphp
<x-layouts.app :title="$offer->number">
    <x-page-header :title="__('Offer').' '.$offer->number" :back="['url' => route('offers.index'), 'label' => __('Offers')]">
        <x-slot:subtitle>
            <x-status :value="$offer->status" />
            <a href="{{ route('companies.show', $offer->company) }}" class="hover:text-brand-700">{{ $offer->company?->name }}</a>
            <span>· <a href="{{ route('deals.show', $offer->deal) }}" class="hover:text-brand-700">{{ $offer->deal?->title }}</a></span>
        </x-slot:subtitle>
        <x-slot:actions>
            <div x-data="{ open: false }" class="relative">
                <x-button variant="secondary" icon="download" @click="open = !open">PDF</x-button>
                <div x-cloak x-show="open" @click.outside="open = false" class="absolute right-0 z-20 mt-1 w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-slate-900/5">
                    @foreach (config('crm.locales') as $code => $label)
                        <a href="{{ route('offers.pdf', [$offer, 'lang' => $code]) }}" class="block px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">{{ $label }}</a>
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

    @if ($canManage && ! $offer->status->isFinal())
        <div class="mb-6 flex flex-col gap-3 rounded-xl border border-brand-100 bg-brand-50/60 p-4 sm:flex-row sm:items-center">
            <p class="flex-1 text-sm text-brand-900">
                @if ($offer->status === OfferStatus::Draft)
                    {{ __('Ready? Mark the offer as sent once the customer has it.') }}
                @else
                    {{ __('Waiting for the customer. Record the answer when it arrives.') }}
                @endif
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach ($offer->status === OfferStatus::Draft ? [OfferStatus::Sent] : [OfferStatus::Accepted, OfferStatus::Rejected] as $next)
                    <form method="POST" action="{{ route('offers.status', $offer) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next->value }}">
                        <x-button type="submit" :variant="$next === OfferStatus::Rejected ? 'secondary' : ($next === OfferStatus::Accepted ? 'success' : 'primary')">
                            {{ match ($next) { OfferStatus::Sent => __('Mark as sent'), OfferStatus::Accepted => __('Accepted'), default => __('Rejected') } }}
                        </x-button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-card :padding="false">
                <div class="grid gap-4 border-b border-slate-100 p-5 text-sm sm:grid-cols-4">
                    <div><p class="text-slate-500">{{ __('Issued') }}</p><p class="mt-0.5 font-medium">{{ format_date($offer->issued_at) }}</p></div>
                    <div><p class="text-slate-500">{{ __('Valid until') }}</p><p @class(['mt-0.5 font-medium', 'text-rose-600' => $offer->status === OfferStatus::Sent && $offer->isExpired()])>{{ format_date($offer->valid_until) }}</p></div>
                    <div><p class="text-slate-500">{{ __('Prepared by') }}</p><p class="mt-0.5 font-medium">{{ $offer->owner?->name ?? '—' }}</p></div>
                    <div><p class="text-slate-500">{{ __('Contact') }}</p><p class="mt-0.5 font-medium">{{ $offer->deal?->contact?->full_name ?? '—' }}</p></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/60">
                            <tr>
                                <th class="table-th">{{ __('Description') }}</th>
                                <th class="table-th text-right">{{ __('Qty') }}</th>
                                <th class="table-th text-right">{{ __('Unit price') }}</th>
                                <th class="table-th text-right">{{ __('Disc. %') }}</th>
                                <th class="table-th text-right">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($offer->items as $item)
                                <tr>
                                    <td class="table-td whitespace-normal">{{ $item->description }}</td>
                                    <td class="table-td text-right">{{ App\Support\Money::number($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2) }} {{ $item->unit }}</td>
                                    <td class="table-td text-right">{{ money($item->unit_price) }}</td>
                                    <td class="table-td text-right">{{ (float) $item->discount_percent ? App\Support\Money::number($item->discount_percent, 0).' %' : '—' }}</td>
                                    <td class="table-td text-right font-medium">{{ money($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end border-t border-slate-100 p-5">
                    <dl class="w-full max-w-xs space-y-1.5 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">{{ __('Subtotal') }}</dt><dd class="font-medium">{{ money($offer->subtotal) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">{{ __('VAT') }} ({{ App\Support\Money::number($offer->vat_rate, 0) }} %)</dt><dd class="font-medium">{{ money($offer->vat_amount) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-200 pt-2 text-base"><dt class="font-semibold">{{ __('Total') }}</dt><dd class="font-bold">{{ money($offer->total) }}</dd></div>
                    </dl>
                </div>
            </x-card>

            @if ($offer->notes)
                <x-card :title="__('Notes & terms')"><p class="whitespace-pre-line text-sm text-slate-700">{{ $offer->notes }}</p></x-card>
            @endif
        </div>

        <div class="space-y-6">
            @if ($offer->parent || $offer->revisions->isNotEmpty())
                <x-card :title="__('Revisions')">
                    <ul class="space-y-2 text-sm">
                        @foreach (collect([$offer->parent])->filter()->merge($offer->revisions)->sortBy('revision') as $rev)
                            <li class="flex items-center justify-between">
                                <a href="{{ route('offers.show', $rev) }}" class="font-mono link">{{ $rev->number }}</a>
                                <x-status :value="$rev->status" />
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif
            @include('partials.history', ['logs' => $offer->auditLogs])
            @can('delete', $offer)
                <div class="flex justify-end"><x-delete-button :action="route('offers.destroy', $offer)" :label="__('Delete offer')" /></div>
            @endcan
        </div>
    </div>
</x-layouts.app>
