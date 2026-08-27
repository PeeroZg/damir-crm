<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $offer->offer_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6 space-y-2">
                <p><strong>Deal:</strong> {{ $offer->deal->title }}</p>
                <p><strong>Tvrtka:</strong> {{ $offer->company->name }}</p>
                <p><strong>Iznos:</strong> {{ number_format($offer->amount, 2) }} €</p>
                <p><strong>Status:</strong> {{ $offer->status }}</p>
                <p><strong>Vrijedi do:</strong> {{ $offer->valid_until?->format('d.m.Y') ?? '-' }}</p>
                <p><strong>Napomene:</strong> {{ $offer->notes ?? '-' }}</p>
            </div>

            <div class="mt-4">
                @if($offer->user_id === auth()->id())
                    <a href="{{ route('offers.edit', $offer) }}" class="text-yellow-600">Uredi</a>
                @endif
                <a href="{{ route('offers.pdf', $offer) }}" class="text-green-600 ml-4">Preuzmi PDF</a>
                <a href="{{ route('offers.index') }}" class="ml-4 text-gray-600">Natrag</a>
            </div>
        </div>
    </div>
</x-app-layout>
