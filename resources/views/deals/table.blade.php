@php $showCompany ??= true; @endphp
@if ($deals->isEmpty())
    <x-empty-state icon="briefcase" :title="__('No deals yet')" :description="$emptyText ?? null" />
@else
    <div class="overflow-x-auto">
        <table class="table">
            <thead><tr><th>{{ __('Deal') }}</th><th>{{ __('Stage') }}</th><th class="text-right">{{ __('Value') }}</th><th>{{ __('Owner') }}</th></tr></thead>
            <tbody>
                @foreach ($deals as $deal)
                    <tr>
                        <td>
                            <a href="{{ route('deals.show', $deal) }}" class="font-medium text-ink-900 hover:underline hover:decoration-volt-500 hover:decoration-2 hover:underline-offset-2">{{ $deal->title }}</a>
                            @if ($showCompany)<p class="text-xs text-ink-500">{{ $deal->company?->name }}</p>@endif
                        </td>
                        <td><x-status :value="$deal->stage" /></td>
                        <td class="text-right font-semibold tnum">{{ money($deal->value) }}</td>
                        <td>@include('partials.owner', ['user' => $deal->owner])</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
