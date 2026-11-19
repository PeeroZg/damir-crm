@php $showCompany ??= true; @endphp
@if ($offers->isEmpty())
    <x-empty-state icon="document" :title="__('No offers yet')" />
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/60">
                <tr>
                    <th class="table-th">{{ __('Number') }}</th>
                    <th class="table-th">{{ __('Status') }}</th>
                    <th class="table-th">{{ __('Issued') }}</th>
                    <th class="table-th text-right">{{ __('Total') }}</th>
                    <th class="table-th"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($offers as $offer)
                    <tr class="hover:bg-slate-50/60">
                        <td class="table-td">
                            <a href="{{ route('offers.show', $offer) }}" class="font-mono font-medium text-slate-900 hover:text-brand-700">{{ $offer->number }}</a>
                            @if ($showCompany)<p class="text-xs text-slate-500">{{ $offer->company?->name }}</p>@endif
                        </td>
                        <td class="table-td"><x-status :value="$offer->status" /></td>
                        <td class="table-td">{{ format_date($offer->issued_at) }}</td>
                        <td class="table-td text-right font-medium">{{ money($offer->total) }}</td>
                        <td class="table-td text-right">
                            <a href="{{ route('offers.pdf', $offer) }}" class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-brand-700"><x-icon name="download" class="h-4 w-4" /> PDF</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
