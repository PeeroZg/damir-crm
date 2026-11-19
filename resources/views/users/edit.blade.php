<x-layouts.app :title="$user->name">
    <x-page-header :title="__('Edit user')" :back="['url' => route('users.index'), 'label' => __('Team')]" />
    <form method="POST" action="{{ route('users.update', $user) }}" class="max-w-2xl">
        @csrf
        @method('PUT')
        <x-card>@include('users.form')</x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('users.index')">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</x-layouts.app>
