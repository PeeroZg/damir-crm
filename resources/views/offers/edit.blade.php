<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Uredi ponudu {{ $offer->offer_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('offers.update', $offer) }}" class="bg-white shadow rounded p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium">Deal</label>
                    <select name="deal_id" class="w-full border rounded p-2">
                        @foreach($deals as $deal)
                            <option value="{{ $deal->id }}" @selected(old('deal_id', $offer->deal_id) == $deal->id)>{{ $deal->title }} ({{ $deal->company->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Iznos (€)</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $offer->amount) }}" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block font-medium">Vrijedi do</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until', $offer->valid_until?->format('Y-m-d')) }}" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block font-medium">Napomene</label>
                    <textarea name="notes" class="w-full border rounded p-2">{{ old('notes', $offer->notes) }}</textarea>
                </div>

                <div>
                    <label class="block font-medium">Status</label>
                    <select name="status" class="w-full border rounded p-2">
                        @foreach(['nacrt' => 'Nacrt', 'poslana' => 'Poslana', 'prihvacena' => 'Prihvaćena', 'odbijena' => 'Odbijena', 'istekla' => 'Istekla'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $offer->status) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Spremi</button>
                <a href="{{ route('offers.index') }}" class="ml-2 text-gray-600">Odustani</a>
            </form>
        </div>
    </div>
</x-app-layout>
