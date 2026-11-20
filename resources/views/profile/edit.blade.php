<x-layouts.app :title="__('Profile')">
<div class="mx-auto max-w-[1120px]">
    <x-page-header :title="__('Profile')">
        <x-slot:description>{{ __('Your account, language and API access.') }}</x-slot:description>
    </x-page-header>
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        <div class="lg:col-span-2">@include('profile.partials.api-tokens')</div>
    </div>
</div>
</x-layouts.app>
