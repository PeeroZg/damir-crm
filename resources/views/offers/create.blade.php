<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova ponuda</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('offers.store') }}" class="bg-white shadow rounded p-6 space-y-4">
                @csrf

                <div>
                    <label class="block font-medium">Deal</label>
                    <select name="deal_id" class="w-full border rounded p-2">
                        <option value="">-- odaberi --</option>
                        @foreach($deals as $deal)
                            <option value="{{ $deal->id }}" @selected(old('deal_id', $selectedDealId) == $deal->id)>{{ $deal->title }} ({{ $deal->company->name }})</option>
                        @endforeach
                    </select>
                    @error('deal_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Iznos (€)</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', 0) }}" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block font-medium">Vrijedi do</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until') }}" class="w-full border rounded p-2">
                    @error('valid_until') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-medium">Napomene</label>
                    <textarea name="notes" class="w-full border rounded p-2">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="block font-medium">Status</label>
                    <select name="status" class="w-full border rounded p-2">
                        <option value="nacrt" selected>Nacrt</option>
                        <option value="poslana">Poslana</option>
                        <option value="prihvacena">Prihvaćena</option>
                        <option value="odbijena">Odbijena</option>
                        <option value="istekla">Istekla</option>
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Spremi</button>
                <a href="{{ route('offers.index') }}" class="ml-2 text-gray-600">Odustani</a>
            </form>
        </div>
    </div>
</x-app-layout>
