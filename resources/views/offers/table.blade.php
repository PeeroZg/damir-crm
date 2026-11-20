@php $showCompany ??= true; @endphp
@if ($offers->isEmpty())
    <x-empty-state icon="document" :title="__('No offers yet')" />
@else
    <div class="overflow-x-auto">
        <table class="table">
            <thead><tr><th>{{ __('Number') }}</th><th>{{ __('Status') }}</th><th>{{ __('Issued') }}</th><th class="text-right">{{ __('Total') }}</th><th class="w-16"><span class="sr-only">PDF</span></th></tr></thead>
            <tbody>
                @foreach ($offers as $offer)
                    <tr>
                        <td>
                            <a href="{{ route('offers.show', $offer) }}" class="font-mono text-sm font-medium text-ink-900 hover:underline">{{ $offer->number }}</a>
                            @if ($showCompany)<p class="text-xs text-ink-500">{{ $offer->company?->name }}</p>@endif
                        </td>
                        <td><x-status :value="$offer->status" /></td>
                        <td class="tnum">{{ format_date($offer->issued_at) }}</td>
                        <td class="text-right font-semibold tnum">{{ money($offer->total) }}</td>
                        <td class="text-right"><x-button size="sm" variant="ghost" icon="download" :href="route('offers.pdf', $offer)">PDF</x-button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
