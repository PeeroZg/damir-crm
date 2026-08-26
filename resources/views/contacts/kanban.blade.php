<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Kontakti — Kanban</h2>
            <a href="{{ route('contacts.index') }}" class="text-blue-600">Tablični prikaz</a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 gap-4">
            @foreach (['aktivan' => 'Aktivan', 'neaktivan' => 'Neaktivan'] as $key => $label)
                <div class="bg-gray-100 rounded-lg p-3 min-h-[300px]">
                    <h3 class="font-semibold mb-3">{{ $label }}</h3>
                    <div class="space-y-2">
                        @foreach ($contacts->where('status', $key) as $contact)
                            <div class="bg-white shadow rounded p-3">
                                <a href="{{ route('contacts.show', $contact) }}" class="font-medium text-blue-600">{{ $contact->first_name }} {{ $contact->last_name }}</a>
                                <div class="text-sm text-gray-500 mb-2">{{ $contact->company->name }}</div>
                                <form method="POST" action="{{ route('contacts.updateStatus', $contact) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="w-full border rounded p-1 text-sm">
                                        <option value="aktivan" {{ $contact->status == 'aktivan' ? 'selected' : '' }}>Aktivan</option>
                                        <option value="neaktivan" {{ $contact->status == 'neaktivan' ? 'selected' : '' }}>Neaktivan</option>
                                    </select>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
