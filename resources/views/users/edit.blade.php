<x-layouts.app :title="$user->name">
<div class="mx-auto max-w-[720px]">
    <x-page-header :title="__('Edit user')" :back="['url' => route('users.index'), 'label' => __('Team')]" />
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <section class="card"><div class="card-body">@include('users.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('users.index')">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
