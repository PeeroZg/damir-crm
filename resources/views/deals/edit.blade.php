<x-layouts.app :title="$deal->title">
    <x-page-header :title="__('Edit deal')" :back="['url' => route('deals.show', $deal), 'label' => $deal->title]" />
    <form method="POST" action="{{ route('deals.update', $deal) }}" class="max-w-3xl">
        @csrf
        @method('PUT')
        <x-card>@include('deals.form')</x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('deals.show', $deal)">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</x-layouts.app>
