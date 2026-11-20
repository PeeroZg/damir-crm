<x-layouts.app :title="__('New contact')">
<div class="mx-auto max-w-[880px]">
    <x-page-header :title="__('New contact')" :back="['url' => route('contacts.index'), 'label' => __('Contacts')]" />
    <form method="POST" action="{{ route('contacts.store') }}">
        @csrf
        <section class="card"><div class="card-body">@include('contacts.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="url()->previous()">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Create contact') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
