<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $company->name }}</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-2 gap-4">
                <div><dt class="text-sm text-gray-500">OIB</dt><dd>{{ $company->oib ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Email</dt><dd>{{ $company->email ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Telefon</dt><dd>{{ $company->phone ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Adresa</dt><dd>{{ $company->address ?? '-' }}</dd></div>
            </dl>
            <a href="{{ route('companies.edit', $company) }}" class="inline-block mt-4 text-blue-600">Uredi tvrtku</a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold mb-3">Kontakti ({{ $company->contacts->count() }})</h3>
            @forelse ($company->contacts as $contact)
                <div class="border-b py-2">{{ $contact->first_name }} {{ $contact->last_name }} — {{ $contact->email }}</div>
            @empty
                <p class="text-gray-500">Nema kontakata.</p>
            @endforelse
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold mb-3">Dealovi ({{ $company->deals->count() }})</h3>
            @forelse ($company->deals as $deal)
                <div class="border-b py-2">{{ $deal->title }} — {{ $deal->status }} — {{ number_format($deal->value, 2) }} €</div>
            @empty
                <p class="text-gray-500">Nema dealova.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
