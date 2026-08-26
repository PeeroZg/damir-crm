<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Uredi tvrtku</h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4">
        <form action="{{ route('companies.update', $company) }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium mb-1">Naziv *</label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" class="w-full border rounded p-2">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">OIB</label>
                <input type="text" name="oib" value="{{ old('oib', $company->oib) }}" class="w-full border rounded p-2">
                @error('oib') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium mb-1">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full border rounded p-2">
            </div>

            <div>
                <label class="block font-medium mb-1">Adresa</label>
                <input type="text" name="address" value="{{ old('address', $company->address) }}" class="w-full border rounded p-2">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Spremi promjene</button>
                <a href="{{ route('companies.index') }}" class="px-4 py-2 rounded border">Odustani</a>
            </div>
        </form>
    </div>
</x-app-layout>
