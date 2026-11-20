<x-layouts.app :title="__('Offers')">
    <x-page-header :title="__('Offers')">
        <x-slot:description>{{ __('Accepting an offer automatically marks its deal as won.') }}</x-slot:description>
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
        'celebrate' => App\Enums\OfferStatus::Accepted->value,
        'collapse' => [App\Enums\OfferStatus::Accepted, App\Enums\OfferStatus::Rejected, App\Enums\OfferStatus::Expired],
        'moreUrl' => fn ($status) => route('offers.index', ['status' => $status->value]),
        'canMove' => fn ($offer) => auth()->user()->can('changeStatus', $offer),
        'titleOf' => fn ($offer) => $offer->number,
    ])
</x-layouts.app>
