<x-layouts.app :title="__('New company')">
    <x-page-header :title="__('New company')" :back="['url' => route('companies.index'), 'label' => __('Companies')]" />
    <form method="POST" action="{{ route('companies.store') }}" class="max-w-3xl">
        @csrf
        <x-card>
            @include('companies.form')
        </x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('companies.index')">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Create company') }}</x-button>
        </div>
    </form>
</x-layouts.app>
