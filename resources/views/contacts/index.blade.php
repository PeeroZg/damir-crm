<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Kontakti</h2>
<div class="space-x-2">
    <a href="{{ route('contacts.kanban') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Kanban</a>
    <a href="{{ route('contacts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Novi kontakt</a>
</div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-3">Ime i prezime</th>
                        <th class="p-3">Tvrtka</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Telefon</th>
                        <th class="p-3">Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                        <tr class="border-b">
                            <td class="p-3">
                                <a href="{{ route('contacts.show', $contact) }}" class="text-blue-600">{{ $contact->first_name }} {{ $contact->last_name }}</a>
                            </td>
                            <td class="p-3">{{ $contact->company->name }}</td>
                            <td class="p-3">{{ $contact->email }}</td>
                            <td class="p-3">{{ $contact->phone }}</td>
                            <td class="p-3 space-x-2">
                                <a href="{{ route('contacts.edit', $contact) }}" class="text-blue-600">Uredi</a>
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Sigurno obrisati?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">Obriši</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $contacts->links() }}</div>
    </div>
</x-app-layout>
