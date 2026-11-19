@php $canEdit = auth()->user()->can('update', $deal); @endphp
<x-layouts.app :title="$deal->title">
    <x-page-header :title="$deal->title" :back="['url' => route('deals.kanban'), 'label' => __('Deal pipeline')]">
        <x-slot:subtitle>
            <x-status :value="$deal->stage" />
            <a href="{{ route('companies.show', $deal->company) }}" class="hover:text-brand-700">{{ $deal->company?->name }}</a>
            @if ($deal->contact)<span>· <a href="{{ route('contacts.show', $deal->contact) }}" class="hover:text-brand-700">{{ $deal->contact->full_name }}</a></span>@endif
        </x-slot:subtitle>
        <x-slot:actions>
            @if ($canEdit)
                <x-button variant="secondary" icon="pencil" :href="route('deals.edit', $deal)">{{ __('Edit') }}</x-button>
            @endif
            @if ($deal->isOpen() && auth()->user()->can('create', [App\Models\Offer::class, $deal]))
                <x-button icon="document" :href="route('offers.create', ['deal_id' => $deal->id])">{{ __('Create offer') }}</x-button>
            @endif
        </x-slot:actions>
    </x-page-header>

    {{-- Stage stepper --}}
    <x-card class="mb-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
            <ol class="flex flex-1 flex-wrap gap-2">
                @foreach ($stages as $stage)
                    @php
                        $current = $deal->stage === $stage;
                        $passed = ! $deal->stage->isClosed() ? $stage->order() < $deal->stage->order() : ($deal->stage === App\Enums\DealStage::Won && ! $stage->isClosed());
                    @endphp
                    <li>
                        <form method="POST" action="{{ route('deals.stage', $deal) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="stage" value="{{ $stage->value }}">
                            <button type="submit" @disabled(! $canEdit || $current)
                                    @class([
                                        'rounded-full px-3 py-1.5 text-xs font-semibold transition disabled:cursor-default',
                                        'bg-brand-600 text-white shadow' => $current && ! $stage->isClosed(),
                                        'bg-emerald-600 text-white shadow' => $current && $stage === App\Enums\DealStage::Won,
                                        'bg-rose-600 text-white shadow' => $current && $stage === App\Enums\DealStage::Lost,
                                        'bg-brand-50 text-brand-700 hover:bg-brand-100' => ! $current && $passed,
                                        'bg-slate-100 text-slate-600 hover:bg-slate-200' => ! $current && ! $passed,
                                    ])>
                                {{ $stage->label() }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ol>
            <div class="flex gap-6 text-sm">
                <div><p class="text-slate-500">{{ __('Value') }}</p><p class="text-lg font-bold text-slate-900">{{ money($deal->value) }}</p></div>
                <div><p class="text-slate-500">{{ __('Weighted') }}</p><p class="text-lg font-bold text-slate-900">{{ money($deal->weightedValue()) }}</p></div>
            </div>
        </div>
        @if ($deal->stage === App\Enums\DealStage::Lost && $deal->lost_reason)
            <p class="mt-4 rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700"><span class="font-semibold">{{ __('Lost reason') }}:</span> {{ $deal->lost_reason }}</p>
        @endif
        @unless ($canEdit)
            <p class="mt-4 text-xs text-slate-500">{{ __('Only :owner or an admin can change this deal.', ['owner' => $deal->owner?->name ?? __('the owner')]) }}</p>
        @endunless
    </x-card>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <x-card :title="__('Details')">
                <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-3">
                    <div><dt class="text-slate-500">{{ __('Owner') }}</dt><dd class="mt-0.5 text-slate-900">{{ $deal->owner?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">{{ __('Expected close') }}</dt><dd class="mt-0.5 text-slate-900">{{ format_date($deal->expected_close_date) }}</dd></div>
                    <div><dt class="text-slate-500">{{ $deal->closed_at ? __('Closed') : __('In stage since') }}</dt><dd class="mt-0.5 text-slate-900">{{ format_date($deal->closed_at ?? $deal->stage_changed_at) }}</dd></div>
                    @if ($deal->description)
                        <div class="sm:col-span-3"><dt class="text-slate-500">{{ __('Description') }}</dt><dd class="mt-0.5 whitespace-pre-line text-slate-900">{{ $deal->description }}</dd></div>
                    @endif
                </dl>
            </x-card>

            <x-card :title="__('Offers')" :padding="false">
                @include('offers.table', ['offers' => $deal->offers, 'showCompany' => false])
            </x-card>
        </div>

        <div class="space-y-6">
            @include('partials.activity-panel', ['subject' => $deal])
            @include('partials.history', ['logs' => $deal->auditLogs])
            @can('delete', $deal)
                <div class="flex justify-end">
                    <x-delete-button :action="route('deals.destroy', $deal)" :label="__('Delete deal')" />
                </div>
            @endcan
        </div>
    </div>
</x-layouts.app>
