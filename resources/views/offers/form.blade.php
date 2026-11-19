{{-- Shared by create/edit. Totals are previewed live in the browser but always recalculated on the server. --}}
<div x-data="offerForm({ items: @js(old('items', $items)), vatRate: @js((float) old('vat_rate', $offer->vat_rate)), currency: @js(config('crm.currency')) })" class="space-y-6">
    <x-card :title="__('Offer details')">
        <div class="grid gap-5 sm:grid-cols-6">
            @if ($offer->exists)
                <div class="sm:col-span-6">
                    <p class="form-label">{{ __('Deal') }}</p>
                    <p class="text-sm text-slate-900">{{ $offer->deal?->title }} <span class="text-slate-500">· {{ $offer->company?->name }}</span></p>
                </div>
            @else
                <x-field :label="__('Deal')" name="deal_id" required class="sm:col-span-6" :hint="$deals->isEmpty() ? __('You have no open deals. Create a deal first.') : null">
                    <select id="deal_id" name="deal_id" class="form-input" required>
                        <option value="">{{ __('Select a deal…') }}</option>
                        @foreach ($deals as $deal)
                            <option value="{{ $deal->id }}" @selected((int) old('deal_id', $offer->deal_id) === $deal->id)>{{ $deal->title }} — {{ $deal->company?->name }}</option>
                        @endforeach
                    </select>
                </x-field>
            @endif

            <x-field :label="__('Issue date')" name="issued_at" required class="sm:col-span-2">
                <input id="issued_at" name="issued_at" type="date" value="{{ old('issued_at', $offer->issued_at?->toDateString()) }}" required class="form-input">
            </x-field>
            <x-field :label="__('Valid until')" name="valid_until" class="sm:col-span-2">
                <input id="valid_until" name="valid_until" type="date" value="{{ old('valid_until', $offer->valid_until?->toDateString()) }}" class="form-input">
            </x-field>
            <x-field :label="__('VAT rate')" name="vat_rate" required class="sm:col-span-2">
                <div class="relative">
                    <input id="vat_rate" name="vat_rate" type="number" step="0.01" min="0" max="100" x-model.number="vatRate" required class="form-input pr-8">
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">%</span>
                </div>
            </x-field>
        </div>
    </x-card>

    <x-card :title="__('Line items')" :padding="false">
        <x-slot:actions>
            <x-button size="sm" variant="secondary" icon="plus" @click="add()">{{ __('Add line') }}</x-button>
        </x-slot:actions>

        @error('items') <p class="px-5 pt-4 text-sm text-rose-600">{{ $message }}</p> @enderror
        @if ($errors->has('items.*'))
            <p class="px-5 pt-4 text-sm text-rose-600">{{ collect($errors->get('items.*'))->flatten()->first() }}</p>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50/60">
                    <tr>
                        <th class="table-th w-8">#</th>
                        <th class="table-th min-w-[16rem]">{{ __('Description') }}</th>
                        <th class="table-th w-24">{{ __('Qty') }}</th>
                        <th class="table-th w-24">{{ __('Unit') }}</th>
                        <th class="table-th w-32">{{ __('Unit price') }}</th>
                        <th class="table-th w-24">{{ __('Disc. %') }}</th>
                        <th class="table-th w-32 text-right">{{ __('Amount') }}</th>
                        <th class="table-th w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(item, index) in items" :key="item.key">
                        <tr class="align-top">
                            <td class="px-4 py-3 text-sm text-slate-400" x-text="index + 1"></td>
                            <td class="px-2 py-2"><input data-description type="text" class="form-input" maxlength="255" x-model="item.description" :name="`items[${index}][description]`" aria-label="{{ __('Description') }}"></td>
                            <td class="px-2 py-2"><input type="number" step="0.01" min="0.01" class="form-input" x-model.number="item.quantity" :name="`items[${index}][quantity]`" aria-label="{{ __('Quantity') }}"></td>
                            <td class="px-2 py-2"><input type="text" maxlength="20" class="form-input" x-model="item.unit" :name="`items[${index}][unit]`" list="units" aria-label="{{ __('Unit') }}"></td>
                            <td class="px-2 py-2"><input type="number" step="0.01" min="0" class="form-input" x-model.number="item.unit_price" :name="`items[${index}][unit_price]`" aria-label="{{ __('Unit price') }}"></td>
                            <td class="px-2 py-2"><input type="number" step="0.01" min="0" max="100" class="form-input" x-model.number="item.discount_percent" :name="`items[${index}][discount_percent]`" aria-label="{{ __('Discount') }}"></td>
                            <td class="px-4 py-3 text-right text-sm font-medium text-slate-900" x-text="money(line(item))"></td>
                            <td class="px-2 py-2">
                                <button type="button" @click="remove(index)" class="rounded p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="{{ __('Remove line') }}"><x-icon name="trash" class="h-4 w-4" /></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <datalist id="units">
                @foreach (['pcs', 'h', 'day', 'month', 'license'] as $unit)<option value="{{ $unit }}">@endforeach
            </datalist>
        </div>

        <div class="flex justify-end border-t border-slate-100 p-5">
            <dl class="w-full max-w-xs space-y-1.5 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">{{ __('Subtotal') }}</dt><dd class="font-medium" x-text="money(subtotal)"></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">{{ __('VAT') }} (<span x-text="vatRate"></span> %)</dt><dd class="font-medium" x-text="money(vat)"></dd></div>
                <div class="flex justify-between border-t border-slate-200 pt-2 text-base"><dt class="font-semibold">{{ __('Total') }}</dt><dd class="font-bold text-slate-900" x-text="money(total)"></dd></div>
            </dl>
        </div>
    </x-card>

    <x-card :title="__('Notes & terms')">
        <textarea name="notes" rows="3" maxlength="5000" class="form-input" placeholder="{{ __('Payment terms, delivery time, anything the customer should know…') }}" aria-label="{{ __('Notes') }}">{{ old('notes', $offer->notes) }}</textarea>
    </x-card>
</div>
