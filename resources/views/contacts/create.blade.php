<x-layouts.app :title="__('New contact')">
    <x-page-header :title="__('New contact')" :back="['url' => route('contacts.index'), 'label' => __('Contacts')]" />
    <form method="POST" action="{{ route('contacts.store') }}" class="max-w-3xl">
        @csrf
        <x-card>@include('contacts.form')</x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="url()->previous()">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Create contact') }}</x-button>
        </div>
    </form>
</x-layouts.app>
