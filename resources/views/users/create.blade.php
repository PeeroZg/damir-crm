<x-layouts.app :title="__('New user')">
<div class="mx-auto max-w-[720px]">
    <x-page-header :title="__('New user')" :back="['url' => route('users.index'), 'label' => __('Team')]" />
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <section class="card"><div class="card-body">@include('users.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('users.index')">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Create user') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
