{{-- Shared by create/edit. Totals are previewed live in the browser but always recalculated on the server. --}}
@php $inline = 'block h-9 w-full rounded-md border border-transparent bg-transparent px-2 text-base text-ink-900 transition hover:border-ink-200 hover:bg-white focus:border-ink-900 focus:bg-white focus:outline-none focus:ring-4 focus:ring-volt-400/50'; @endphp
<div class="flex min-w-0 flex-col gap-3">
    <section class="card" aria-labelledby="od-h">
        <div class="card-header"><h2 id="od-h" class="card-title">{{ __('Offer details') }}</h2></div>
        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">
            @if ($offer->exists)
                <div class="sm:col-span-3">
                    <p class="label">{{ __('Deal') }}</p>
                    <p class="text-base text-ink-900">{{ $offer->deal?->title }} <span class="text-ink-500">· {{ $offer->company?->name }}</span></p>
                </div>
            @else
                <x-field :label="__('Deal')" name="deal_id" required class="sm:col-span-3" :hint="$deals->isEmpty() ? __('You have no open deals. Create a deal first.') : null">
                    <select id="deal_id" name="deal_id" required @class(['select', 'input-error' => $errors->has('deal_id')])>
                        <option value="">{{ __('Select a deal…') }}</option>
                        @foreach ($deals as $deal)
                            <option value="{{ $deal->id }}" @selected((int) old('deal_id', $offer->deal_id) === $deal->id)>{{ $deal->title }} — {{ $deal->company?->name }}</option>
                        @endforeach
                    </select>
                </x-field>
            @endif
            <x-field :label="__('Issue date')" name="issued_at" required>
                <input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at', $offer->issued_at?->toDateString()) }}" required class="input tnum">
            </x-field>
            <x-field :label="__('Valid until')" name="valid_until">
                <input id="valid_until" name="valid_until" type="date" value="{{ old('valid_until', $offer->valid_until?->toDateString()) }}" @class(['input tnum', 'input-error' => $errors->has('valid_until')])>
            </x-field>
            <x-field :label="__('VAT rate')" name="vat_rate" required>
                <div class="relative">
                    <input id="vat_rate" name="vat_rate" type="number" step="0.01" min="0" max="100" x-model.number="vatRate" required class="input pr-9 tnum">
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-ink-500">%</span>
                </div>
            </x-field>
        </div>
    </section>

    <section class="card overflow-hidden" aria-labelledby="li-h">
        <div class="card-header">
            <h2 id="li-h" class="card-title flex items-center gap-2">{{ __('Line items') }} <span class="count" x-text="items.length"></span></h2>
            <x-button size="sm" variant="secondary" icon="plus" @click="add()">{{ __('Add line') }}</x-button>
        </div>
        @error('items') <p class="error px-5 pt-4">{{ $message }}</p> @enderror
        @if ($errors->has('items.*'))
            <p class="error px-5 pt-4">{{ collect($errors->get('items.*'))->flatten()->first() }}</p>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-separate border-spacing-0 text-left">
                <thead>
                    <tr class="text-2xs font-medium uppercase text-ink-500">
                        <th class="h-10 w-10 border-b border-ink-150 bg-ink-25 pl-5 font-medium">#</th>
                        <th class="border-b border-ink-150 bg-ink-25 px-2 font-medium">{{ __('Description') }}</th>
                        <th class="w-[80px] border-b border-ink-150 bg-ink-25 px-2 pr-4 text-right font-medium">{{ __('Qty') }}</th>
                        <th class="w-[88px] border-b border-ink-150 bg-ink-25 px-2 font-medium">{{ __('Unit') }}</th>
                        <th class="w-[120px] border-b border-ink-150 bg-ink-25 px-2 pr-4 text-right font-medium">{{ __('Unit price') }}</th>
                        <th class="w-[84px] border-b border-ink-150 bg-ink-25 px-2 pr-4 text-right font-medium">{{ __('Disc. %') }}</th>
                        <th class="w-[124px] border-b border-ink-150 bg-ink-25 px-2 text-right font-medium">{{ __('Amount') }}</th>
                        <th class="w-12 border-b border-ink-150 bg-ink-25 pr-3"><span class="sr-only">{{ __('Remove line') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="item.key">
                        <tr class="group">
                            <td class="border-b border-ink-100 pl-5 text-sm text-ink-500 tnum" x-text="index + 1"></td>
                            <td class="border-b border-ink-100 px-1 py-2"><input data-description class="{{ $inline }}" maxlength="255" x-model="item.description" :name="`items[${index}][description]`" placeholder="{{ __('What are you selling?') }}" aria-label="{{ __('Description') }}"></td>
                            <td class="border-b border-ink-100 px-1"><input type="number" step="0.01" min="0.01" class="{{ $inline }} text-right tnum" x-model.number="item.quantity" :name="`items[${index}][quantity]`" aria-label="{{ __('Quantity') }}"></td>
                            <td class="border-b border-ink-100 px-1"><input class="{{ $inline }}" maxlength="20" list="units" x-model="item.unit" :name="`items[${index}][unit]`" aria-label="{{ __('Unit') }}"></td>
                            <td class="border-b border-ink-100 px-1"><input type="number" step="0.01" min="0" class="{{ $inline }} text-right tnum" x-model.number="item.unit_price" :name="`items[${index}][unit_price]`" aria-label="{{ __('Unit price') }}"></td>
                            <td class="border-b border-ink-100 px-1"><input type="number" step="0.01" min="0" max="100" class="{{ $inline }} text-right tnum" x-model.number="item.discount_percent" :name="`items[${index}][discount_percent]`" aria-label="{{ __('Discount') }}"></td>
                            <td class="border-b border-ink-100 px-2 text-right text-base font-semibold text-ink-900 tnum" x-text="money(line(item))"></td>
                            <td class="border-b border-ink-100 pr-3 text-right"><button type="button" class="btn btn-icon btn-ghost h-8 w-8 text-ink-400 hover:bg-lost-bg hover:text-lost-fg" @click="remove(index)" aria-label="{{ __('Remove line') }}"><x-icon name="trash" class="h-4 w-4" /></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <datalist id="units">@foreach (['pcs', 'h', 'day', 'month', 'license'] as $unit)<option value="{{ $unit }}">@endforeach</datalist>
        </div>
        <div class="flex flex-col gap-4 p-3 sm:flex-row sm:items-start sm:justify-between sm:p-5">
            <button type="button" class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-ink-500 transition hover:bg-ink-50 hover:text-ink-900" @click="add()"><span class="flex h-5 w-5 items-center justify-center rounded-md border border-dashed border-ink-300"><x-icon name="plus" class="h-3.5 w-3.5" /></span>{{ __('Add line') }}</button>
            <dl class="w-full space-y-2 px-2 text-base sm:max-w-[320px] sm:px-0">
                <div class="flex justify-between"><dt class="text-ink-500">{{ __('Subtotal') }}</dt><dd class="font-medium text-ink-900 tnum" x-text="money(subtotal)"></dd></div>
                <div class="flex justify-between"><dt class="text-ink-500">{{ __('VAT') }} (<span x-text="vatRate"></span> %)</dt><dd class="font-medium text-ink-900 tnum" x-text="money(vat)"></dd></div>
                <div class="flex items-baseline justify-between border-t border-ink-150 pt-2.5"><dt class="font-semibold text-ink-900">{{ __('Total') }}</dt><dd class="relative font-display text-2xl font-bold text-ink-900 tnum"><span class="absolute inset-x-[-4px] bottom-[2px] h-[10px] rounded-sm bg-volt-400" aria-hidden="true"></span><span class="relative" x-text="money(total)"></span></dd></div>
            </dl>
        </div>
    </section>

    <section class="card" aria-labelledby="nt-h">
        <div class="card-header"><h2 id="nt-h" class="card-title">{{ __('Notes & terms') }}</h2><span class="text-xs text-ink-500">{{ __('Printed on the PDF') }}</span></div>
        <div class="p-5"><label for="notes" class="sr-only">{{ __('Notes & terms') }}</label><textarea id="notes" name="notes" maxlength="5000" class="textarea" placeholder="{{ __('Payment terms, delivery time, anything the customer should know…') }}">{{ old('notes', $offer->notes) }}</textarea></div>
    </section>
</div>
