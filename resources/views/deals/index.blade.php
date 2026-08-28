<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dealovi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('deals.kanban') }}" class="text-blue-600 underline">Kanban prikaz</a>
                <a href="{{ route('deals.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Novi deal</a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="p-3">Naslov</th>
                            <th class="p-3">Tvrtka</th>
                            <th class="p-3">Vrijednost</th>
                            <th class="p-3">Status</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deals as $deal)
                            <tr class="border-b">
                                <td class="p-3">{{ $deal->title }}</td>
                                <td class="p-3">{{ $deal->company->name }}</td>
                                <td class="p-3">{{ number_format($deal->value, 2) }} €</td>
                                <td class="p-3">{{ $deal->status }}</td>
                                <td class="p-3">
                                    <a href="{{ route('deals.show', $deal) }}" class="text-blue-600">Pregled</a>
                                    @if($deal->user_id === auth()->id())
                                        <a href="{{ route('deals.edit', $deal) }}" class="text-yellow-600 ml-2">Uredi</a>
                                        <form action="{{ route('deals.destroy', $deal) }}" method="POST" class="inline" onsubmit="return confirm('Sigurno obrisati?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 ml-2">Obriši</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $deals->links() }}</div>
        </div>
    </div>
</x-app-layout>
