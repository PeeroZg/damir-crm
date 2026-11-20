<x-layouts.app :title="__('New deal')">
<div class="mx-auto max-w-[880px]">
    <x-page-header :title="__('New deal')" :back="['url' => route('deals.kanban'), 'label' => __('Deal pipeline')]" />
    <form method="POST" action="{{ route('deals.store') }}">
        @csrf
        <section class="card"><div class="card-body">@include('deals.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('deals.kanban')">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Create deal') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
