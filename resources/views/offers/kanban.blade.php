<x-layouts.app :title="__('Offers')">
    <x-page-header :title="__('Offers')" :subtitle="__('Accepting an offer automatically marks its deal as won.')">
        <x-slot:actions>
            <x-view-toggle :list="route('offers.index')" :kanban="route('offers.kanban')" active="kanban" />
            <x-button icon="plus" :href="route('offers.create')">{{ __('New offer') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.kanban', [
        'action' => route('offers.kanban'),
        'url' => route('offers.status', '__ID__'),
        'field' => 'status',
        'columns' => $columns,
        'items' => $offers,
        'card' => 'offers.card',
        'valueField' => 'total',
        'canMove' => fn ($offer) => auth()->user()->can('changeStatus', $offer),
    ])
</x-layouts.app>
