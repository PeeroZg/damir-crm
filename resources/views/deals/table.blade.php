@php $showCompany ??= true; @endphp
@if ($deals->isEmpty())
    <x-empty-state icon="briefcase" :title="__('No deals yet')" />
@else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/60">
                <tr>
                    <th class="table-th">{{ __('Deal') }}</th>
                    <th class="table-th">{{ __('Stage') }}</th>
                    <th class="table-th text-right">{{ __('Value') }}</th>
                    <th class="table-th">{{ __('Owner') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($deals as $deal)
                    <tr class="hover:bg-slate-50/60">
                        <td class="table-td">
                            <a href="{{ route('deals.show', $deal) }}" class="font-medium text-slate-900 hover:text-brand-700">{{ $deal->title }}</a>
                            @if ($showCompany)<p class="text-xs text-slate-500">{{ $deal->company?->name }}</p>@endif
                        </td>
                        <td class="table-td"><x-status :value="$deal->stage" /></td>
                        <td class="table-td text-right font-medium">{{ money($deal->value) }}</td>
                        <td class="table-td">{{ $deal->owner?->name ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
