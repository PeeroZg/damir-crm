<x-layouts.app :title="$contact->full_name">
    <x-page-header :title="__('Edit contact')" :back="['url' => route('contacts.show', $contact), 'label' => $contact->full_name]" />
    <form method="POST" action="{{ route('contacts.update', $contact) }}" class="max-w-3xl">
        @csrf
        @method('PUT')
        <x-card>@include('contacts.form')</x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('contacts.show', $contact)">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</x-layouts.app>
