<x-layouts.app :title="__('Deal pipeline')">
    <x-page-header :title="__('Deal pipeline')" :subtitle="__('Drag deals through the stages. Column totals update live.')">
        <x-slot:actions>
            <x-view-toggle :list="route('deals.index')" :kanban="route('deals.kanban')" active="kanban" />
            <x-button icon="plus" :href="route('deals.create')">{{ __('New deal') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.kanban', [
        'action' => route('deals.kanban'),
        'url' => route('deals.stage', '__ID__'),
        'field' => 'stage',
        'columns' => $columns,
        'items' => $deals,
        'card' => 'deals.card',
        'reasonFor' => App\Enums\DealStage::Lost->value,
        'mineToggle' => true,
        'canMove' => fn ($deal) => auth()->user()->can('update', $deal),
    ])
</x-layouts.app>
