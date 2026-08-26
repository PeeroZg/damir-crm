<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Uredi deal</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('deals.update', $deal) }}" class="bg-white shadow rounded p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium">Naslov</label>
                    <input type="text" name="title" value="{{ old('title', $deal->title) }}" class="w-full border rounded p-2">
                    @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium">Tvrtka</label>
                    <select name="company_id" class="w-full border rounded p-2">
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected(old('company_id', $deal->company_id) == $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Kontakt (opcionalno)</label>
                    <select name="contact_id" class="w-full border rounded p-2">
                        <option value="">-- bez kontakta --</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}" @selected(old('contact_id', $deal->contact_id) == $contact->id)>{{ $contact->first_name }} {{ $contact->last_name }} ({{ $contact->company->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Opis</label>
                    <textarea name="description" class="w-full border rounded p-2">{{ old('description', $deal->description) }}</textarea>
                </div>

                <div>
                    <label class="block font-medium">Vrijednost (€)</label>
                    <input type="number" step="0.01" name="value" value="{{ old('value', $deal->value) }}" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block font-medium">Status</label>
                    <select name="status" class="w-full border rounded p-2">
                        @foreach(['lead' => 'Lead', 'kvalificiran' => 'Kvalificiran', 'ponuda_poslana' => 'Ponuda poslana', 'pregovori' => 'Pregovori', 'dobiven' => 'Dobiven', 'izgubljen' => 'Izgubljen'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $deal->status) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Razlog gubitka (ako je izgubljen)</label>
                    <input type="text" name="lost_reason" value="{{ old('lost_reason', $deal->lost_reason) }}" class="w-full border rounded p-2">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Spremi</button>
                <a href="{{ route('deals.index') }}" class="ml-2 text-gray-600">Odustani</a>
            </form>
        </div>
    </div>
</x-app-layout>
