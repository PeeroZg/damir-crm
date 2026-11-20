<x-layouts.app :title="$deal->title">
<div class="mx-auto max-w-[880px]">
    <x-page-header :title="__('Edit deal')" :back="['url' => route('deals.show', $deal), 'label' => $deal->title]" />
    <form method="POST" action="{{ route('deals.update', $deal) }}">
        @csrf
        @method('PUT')
        <section class="card"><div class="card-body">@include('deals.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('deals.show', $deal)">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
