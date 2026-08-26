<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Novi kontakt</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4">
        <form action="{{ route('contacts.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf

            <div>
                <label class="block font-medium mb-1">Tvrtka *</label>
                <select name="company_id" class="w-full border rounded p-2">
                    <option value="">-- Odaberi tvrtku --</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Ime *</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full border rounded p-2">
                @error('first_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Prezime *</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full border rounded p-2">
                @error('last_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium mb-1">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium mb-1">Pozicija</label>
                <input type="text" name="position" value="{{ old('position') }}" class="w-full border rounded p-2">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Spremi</button>
                <a href="{{ route('contacts.index') }}" class="px-4 py-2 rounded border">Odustani</a>
            </div>
        </form>
    </div>
</x-app-layout>
