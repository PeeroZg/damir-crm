<x-layouts.app :title="$company->name">
<div class="mx-auto max-w-[880px]">
    <x-page-header :title="__('Edit company')" :back="['url' => route('companies.show', $company), 'label' => $company->name]" />
    <form method="POST" action="{{ route('companies.update', $company) }}">
        @csrf
        @method('PUT')
        <section class="card"><div class="card-body">@include('companies.form')</div></section>
        <div class="mt-4 flex justify-end gap-2">
            <x-button variant="secondary" :href="route('companies.show', $company)">{{ __('Cancel') }}</x-button>
            <x-button type="submit" icon="check">{{ __('Save changes') }}</x-button>
        </div>
    </form>
</div>
</x-layouts.app>
