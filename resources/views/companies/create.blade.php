<x-layouts.app :title="__('New company')">
<div class="mx-auto max-w-[880px]">
    <x-page-header :title="__('New company')" :back="['url' => route('companies.index'), 'label' => __('Companies')]" />
    <form method="POST" action="{{ route('companies.store') }}">
        @csrf
        <section class="card"><div class="card-body">@include('companies.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('companies.index')">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Create company') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
