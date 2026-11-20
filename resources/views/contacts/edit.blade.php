<x-layouts.app :title="$contact->full_name">
<div class="mx-auto max-w-[880px]">
    <x-page-header :title="__('Edit contact')" :back="['url' => route('contacts.show', $contact), 'label' => $contact->full_name]" />
    <form method="POST" action="{{ route('contacts.update', $contact) }}">
        @csrf
        @method('PUT')
        <section class="card"><div class="card-body">@include('contacts.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('contacts.show', $contact)">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
