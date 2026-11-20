<x-layouts.app :title="$offer->number">
<form method="POST" action="{{ route('offers.update', $offer) }}" class="mx-auto max-w-[1120px]"
      x-data="offerForm({ items: @js(old('items', $items)), vatRate: @js((float) old('vat_rate', $offer->vat_rate)), currency: @js(config('crm.currency')) })">
    @csrf
    @method('PUT')
    <x-page-header :title="__('Edit offer :number', ['number' => $offer->number])" :back="['url' => route('offers.show', $offer), 'label' => $offer->number]" />
    @include('offers.form')
    <div class="action-bar">
        <div class="flex items-baseline gap-2"><span class="text-sm text-ink-300">{{ __('Total incl. VAT') }}</span><span class="font-display text-xl font-bold tnum" x-text="money(total)"></span></div>
        <span class="hidden items-center gap-1.5 rounded-full bg-white/10 px-2 py-0.5 text-xs text-ink-200 sm:inline-flex"><span class="h-1.5 w-1.5 rounded-full bg-draft-solid"></span>{{ __('Draft') }}</span>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('offers.show', $offer) }}" class="btn text-ink-200 hover:bg-white/10 hover:text-white">{{ __('Cancel') }}</a>
            <x-button type="submit" variant="accent" icon="check">{{ __('Save changes') }}</x-button>
        </div>
    </div>
</form>
</x-layouts.app>
