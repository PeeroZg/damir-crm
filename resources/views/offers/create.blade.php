<x-layouts.app :title="__('New offer')">
    <x-page-header :title="__('New offer')" :subtitle="__('The offer number is assigned automatically.')" :back="['url' => route('offers.index'), 'label' => __('Offers')]" />
    <form method="POST" action="{{ route('offers.store') }}" class="max-w-5xl">
        @csrf
        @include('offers.form')
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="url()->previous()">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Create offer') }}</x-button>
        </div>
    </form>
</x-layouts.app>
