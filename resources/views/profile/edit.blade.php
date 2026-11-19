<x-layouts.app :title="__('Profile')">
    <x-page-header :title="__('Profile')" :subtitle="__('Your account, language and API access.')" />

    <div class="grid gap-6 lg:grid-cols-2">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        <div class="lg:col-span-2">
            @include('profile.partials.api-tokens')
        </div>
    </div>
</x-layouts.app>
