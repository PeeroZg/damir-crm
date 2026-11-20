<x-layouts.app :title="__('Contacts')">
    <x-page-header :title="__('Contacts')">
        <x-slot:description>{{ __('Track how far along each relationship is.') }}</x-slot:description>
        <x-slot:actions>
            <x-view-toggle :list="route('contacts.index')" :kanban="route('contacts.kanban')" active="kanban" />
            <x-button icon="plus" :href="route('contacts.create')">{{ __('New contact') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.kanban', [
        'action' => route('contacts.kanban'),
        'url' => route('contacts.status', '__ID__'),
        'field' => 'status',
        'columns' => $columns,
        'items' => $contacts,
        'card' => 'contacts.card',
        'totals' => false,
        'titleOf' => fn ($contact) => $contact->full_name,
    ])
</x-layouts.app>
