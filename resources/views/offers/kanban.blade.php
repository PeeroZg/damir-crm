<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ponude — Kanban</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('offers.index') }}" class="text-blue-600 underline">Lista prikaz</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @php
                    $statuses = [
                        'nacrt' => 'Nacrt',
                        'poslana' => 'Poslana',
                        'prihvacena' => 'Prihvaćena',
                        'odbijena' => 'Odbijena',
                        'istekla' => 'Istekla',
                    ];
                @endphp

                @foreach($statuses as $key => $label)
                    <div class="bg-gray-100 rounded p-3">
                        <h3 class="font-semibold mb-3">{{ $label }}</h3>

                        @foreach($offers->where('status', $key) as $offer)
                            <div class="bg-white rounded shadow p-3 mb-3">
                                <p class="font-medium">{{ $offer->offer_number }}</p>
                                <p class="text-sm text-gray-500">{{ $offer->deal->title }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($offer->amount, 2) }} €</p>

                                <form method="POST" action="{{ route('offers.updateStatus', $offer) }}" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="w-full border rounded p-1 text-sm">
                                        @foreach($statuses as $optKey => $optLabel)
                                            <option value="{{ $optKey }}" @selected($offer->status === $optKey)>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                </form>

                                <a href="{{ route('offers.show', $offer) }}" class="text-xs text-blue-600 mt-2 inline-block">Detalji</a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
