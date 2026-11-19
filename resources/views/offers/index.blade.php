<x-layouts.app :title="__('Offers')">
    <x-page-header :title="__('Offers')" :subtitle="__('Quotes sent to customers, with line items and VAT.')">
        <x-slot:actions>
            <x-view-toggle :list="route('offers.index')" :kanban="route('offers.kanban')" active="list" />
            <x-button icon="plus" :href="route('offers.create')">{{ __('New offer') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <x-filter-bar :action="route('offers.index')" :placeholder="__('Search by number, company or deal…')">
            <select name="status" class="form-input sm:w-40" aria-label="{{ __('Status') }}">
                <option value="">{{ __('All statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <select name="owner" class="form-input sm:w-40" aria-label="{{ __('Owner') }}">
                <option value="">{{ __('All owners') }}</option>
                <option value="me" @selected(request('owner') === 'me')>{{ __('My offers') }}</option>
            </select>
        </x-filter-bar>

        @if ($offers->isEmpty())
            <x-empty-state icon="document" :title="__('No offers found')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/60">
                        <tr>
                            <th class="table-th">{{ __('Number') }}</th>
                            <th class="table-th">{{ __('Company / deal') }}</th>
                            <th class="table-th">{{ __('Status') }}</th>
                            <th class="table-th">{{ __('Issued') }}</th>
                            <th class="table-th">{{ __('Valid until') }}</th>
                            <th class="table-th text-right">{{ __('Total') }}</th>
                            <th class="table-th"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($offers as $offer)
                            <tr class="hover:bg-slate-50/60">
                                <td class="table-td"><a href="{{ route('offers.show', $offer) }}" class="font-mono font-medium text-slate-900 hover:text-brand-700">{{ $offer->number }}</a></td>
                                <td class="table-td">
                                    <p class="font-medium text-slate-900">{{ $offer->company?->name }}</p>
                                    <p class="max-w-xs truncate text-xs text-slate-500">{{ $offer->deal?->title }}</p>
                                </td>
                                <td class="table-td"><x-status :value="$offer->status" /></td>
                                <td class="table-td">{{ format_date($offer->issued_at) }}</td>
                                <td @class(['table-td', 'font-medium text-rose-600' => $offer->status === App\Enums\OfferStatus::Sent && $offer->isExpired()])>{{ format_date($offer->valid_until) }}</td>
                                <td class="table-td text-right font-semibold">{{ money($offer->total) }}</td>
                                <td class="table-td text-right">
                                    <a href="{{ route('offers.pdf', $offer) }}" class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-brand-700"><x-icon name="download" class="h-4 w-4" /> PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $offers->links() }}
        @endif
    </x-card>
</x-layouts.app>
