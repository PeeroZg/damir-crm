<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $contact->first_name }} {{ $contact->last_name }}</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-2 gap-4">
                <div><dt class="text-sm text-gray-500">Tvrtka</dt><dd><a href="{{ route('companies.show', $contact->company) }}" class="text-blue-600">{{ $contact->company->name }}</a></dd></div>
                <div><dt class="text-sm text-gray-500">Pozicija</dt><dd>{{ $contact->position ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Email</dt><dd>{{ $contact->email ?? '-' }}</dd></div>
                <div><dt class="text-sm text-gray-500">Telefon</dt><dd>{{ $contact->phone ?? '-' }}</dd></div>
            </dl>
            <a href="{{ route('contacts.edit', $contact) }}" class="inline-block mt-4 text-blue-600">Uredi kontakt</a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold mb-3">Dealovi ({{ $contact->deals->count() }})</h3>
            @forelse ($contact->deals as $deal)
                <div class="border-b py-2">{{ $deal->title }} — {{ $deal->status }} — {{ number_format($deal->value, 2) }} €</div>
            @empty
                <p class="text-gray-500">Nema dealova.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
