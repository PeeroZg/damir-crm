<x-layouts.app :title="__('Offers')">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :title="__('Offers')">
        <x-slot:description>{{ __('Quotes sent to customers, with line items and VAT.') }}</x-slot:description>
        <x-slot:actions>
            <x-view-toggle :list="route('offers.index')" :kanban="route('offers.kanban')" active="list" />
            <x-button icon="plus" :href="route('offers.create')">{{ __('New offer') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <section class="card overflow-hidden">
        <x-filter-bar :action="route('offers.index')" :placeholder="__('Search by number, company or deal…')">
            <label for="status" class="sr-only">{{ __('Status') }}</label>
            <select id="status" name="status" class="select w-full sm:w-[170px]">
                <option value="">{{ __('All statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <label for="owner" class="sr-only">{{ __('Owner') }}</label>
            <select id="owner" name="owner" class="select w-full sm:w-[170px]">
                <option value="">{{ __('All owners') }}</option>
                <option value="me" @selected(request('owner') === 'me')>{{ __('My offers') }}</option>
            </select>
        </x-filter-bar>

        @if ($offers->isEmpty())
            <x-empty-state icon="document" :title="__('No offers found')" />
        @else
            <div class="overflow-x-auto">
                <table class="table min-w-[920px]">
                    <thead>
                        <tr>
                            <th>{{ __('Number') }}</th>
                            <th>{{ __('Company / deal') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Issued') }}</th>
                            <th>{{ __('Valid until') }}</th>
                            <th class="text-right">{{ __('Total') }}</th>
                            <th class="w-16"><span class="sr-only">PDF</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($offers as $offer)
                            <tr>
                                <td><a href="{{ route('offers.show', $offer) }}" class="font-mono text-sm font-medium text-ink-900 hover:underline">{{ $offer->number }}</a></td>
                                <td>
                                    <p class="font-medium text-ink-900">{{ $offer->company?->name }}</p>
                                    <p class="max-w-xs truncate text-xs text-ink-500">{{ $offer->deal?->title }}</p>
                                </td>
                                <td><x-status :value="$offer->status" /></td>
                                <td class="tnum">{{ format_date($offer->issued_at) }}</td>
                                <td @class(['tnum', 'font-medium text-lost-fg' => $offer->status === App\Enums\OfferStatus::Sent && $offer->isExpired()])>{{ format_date($offer->valid_until) }}</td>
                                <td class="text-right font-semibold tnum">{{ money($offer->total) }}</td>
                                <td class="text-right"><x-button size="sm" variant="ghost" icon="download" :href="route('offers.pdf', $offer)">PDF</x-button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $offers->links() }}
        @endif
    </section>
</div>
</x-layouts.app>
