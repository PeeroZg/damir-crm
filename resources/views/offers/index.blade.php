<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ponude</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('offers.kanban') }}" class="text-blue-600 underline">Kanban prikaz</a>
                <a href="{{ route('offers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Nova ponuda</a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="p-3">Broj ponude</th>
                            <th class="p-3">Deal</th>
                            <th class="p-3">Tvrtka</th>
                            <th class="p-3">Iznos</th>
                            <th class="p-3">Status</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offers as $offer)
                            <tr class="border-b">
                                <td class="p-3">{{ $offer->offer_number }}</td>
                                <td class="p-3">{{ $offer->deal->title }}</td>
                                <td class="p-3">{{ $offer->company->name }}</td>
                                <td class="p-3">{{ number_format($offer->amount, 2) }} €</td>
                                <td class="p-3">{{ $offer->status }}</td>
                                <td class="p-3">
                                    <a href="{{ route('offers.show', $offer) }}" class="text-blue-600">Pregled</a>
                                    <a href="{{ route('offers.edit', $offer) }}" class="text-yellow-600 ml-2">Uredi</a>
                                    <a href="{{ route('offers.pdf', $offer) }}" class="text-green-600 ml-2">PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $offers->links() }}</div>
        </div>
    </div>
</x-app-layout>
