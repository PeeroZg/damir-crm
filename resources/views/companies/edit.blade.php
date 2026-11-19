<x-layouts.app :title="$company->name">
    <x-page-header :title="__('Edit company')" :back="['url' => route('companies.show', $company), 'label' => $company->name]" />
    <form method="POST" action="{{ route('companies.update', $company) }}" class="max-w-3xl">
        @csrf
        @method('PUT')
        <x-card>
            @include('companies.form')
        </x-card>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('companies.show', $company)">{{ __('Cancel') }}</x-button>
            <x-button type="submit">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</x-layouts.app>
