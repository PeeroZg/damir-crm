<x-layouts.app :title="__('Companies')">
    <x-page-header :title="__('Companies')" :subtitle="__('Drag a card to change the relationship status.')">
        <x-slot:actions>
            <x-view-toggle :list="route('companies.index')" :kanban="route('companies.kanban')" active="kanban" />
            <x-button icon="plus" :href="route('companies.create')">{{ __('New company') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.kanban', [
        'action' => route('companies.kanban'),
        'url' => route('companies.status', '__ID__'),
        'field' => 'status',
        'columns' => $columns,
        'items' => $companies,
        'card' => 'companies.card',
        'totals' => false,
    ])
</x-layouts.app>
