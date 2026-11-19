<x-layouts.app :title="$offer->number">
    <x-page-header :title="__('Edit offer :number', ['number' => $offer->number])" :back="['url' => route('offers.show', $offer), 'label' => $offer->number]" />
    <form method="POST" action="{{ route('offers.update', $offer) }}" class="max-w-5xl">
        @csrf
        @method('PUT')
        @include('offers.form')
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('offers.show', $offer)">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</x-layouts.app>
