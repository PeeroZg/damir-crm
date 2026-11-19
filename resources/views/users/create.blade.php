<x-layouts.app :title="__('New user')">
    <x-page-header :title="__('New user')" :back="['url' => route('users.index'), 'label' => __('Team')]" />
    <form method="POST" action="{{ route('users.store') }}" class="max-w-2xl">
        @csrf
        <x-card>@include('users.form')</x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('users.index')">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Create user') }}</x-button>
        </div>
    </form>
</x-layouts.app>
