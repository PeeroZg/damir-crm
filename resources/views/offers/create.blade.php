<x-layouts.app :title="__('New offer')">
<form method="POST" action="{{ route('offers.store') }}" class="mx-auto max-w-[1120px]"
      x-data="offerForm({ items: @js(old('items', $items)), vatRate: @js((float) old('vat_rate', $offer->vat_rate)), currency: @js(config('crm.currency')) })">
    @csrf
    <x-page-header :title="__('New offer')" :back="['url' => route('offers.index'), 'label' => __('Offers')]">
        <x-slot:description>
            <span class="flex flex-wrap items-center gap-2">{{ __('The offer number is assigned automatically.') }} <span class="rounded-md bg-ink-100 px-1.5 py-0.5 font-mono text-xs text-ink-600">OFF-{{ now()->year }}-####</span></span>
        </x-slot:description>
    </x-page-header>
    @include('offers.form')
    <div class="action-bar">
        <div class="flex items-baseline gap-2"><span class="text-sm text-ink-300">{{ __('Total incl. VAT') }}</span><span class="font-display text-xl font-bold tnum" x-text="money(total)"></span></div>
        <span class="hidden items-center gap-1.5 rounded-full bg-white/10 px-2 py-0.5 text-xs text-ink-200 sm:inline-flex"><span class="h-1.5 w-1.5 rounded-full bg-draft-solid"></span>{{ __('Draft') }}</span>
        <div class="ml-auto flex gap-2">
            <a href="{{ url()->previous() }}" class="btn text-ink-200 hover:bg-white/10 hover:text-white">{{ __('Cancel') }}</a>
            <x-button type="submit" variant="accent" icon="check">{{ __('Create offer') }}</x-button>
        </div>
    </div>
</form>
</x-layouts.app>
