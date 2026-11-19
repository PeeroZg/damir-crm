<x-layouts.app :title="__('New deal')">
    <x-page-header :title="__('New deal')" :back="['url' => route('deals.kanban'), 'label' => __('Deal pipeline')]" />
    <form method="POST" action="{{ route('deals.store') }}" class="max-w-3xl">
        @csrf
        <x-card>@include('deals.form')</x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('deals.kanban')">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Create deal') }}</x-button>
        </div>
    </form>
</x-layouts.app>
