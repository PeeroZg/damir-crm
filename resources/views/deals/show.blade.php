<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $deal->title }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6 space-y-2">
                <p><strong>Tvrtka:</strong> {{ $deal->company->name }}</p>
                <p><strong>Kontakt:</strong> {{ $deal->contact ? $deal->contact->first_name . ' ' . $deal->contact->last_name : '-' }}</p>
                <p><strong>Opis:</strong> {{ $deal->description ?? '-' }}</p>
                <p><strong>Vrijednost:</strong> {{ number_format($deal->value, 2) }} €</p>
                <p><strong>Status:</strong> {{ $deal->status }}</p>
                <p><strong>Razlog gubitka:</strong> {{ $deal->lost_reason ?? '-' }}</p>
                <p><strong>Zatvoreno:</strong> {{ $deal->closed_at?->format('d.m.Y H:i') ?? '-' }}</p>
            </div>

            <div class="mt-4">
                @if($deal->user_id === auth()->id())
                    <a href="{{ route('deals.edit', $deal) }}" class="text-yellow-600">Uredi</a>
                @endif
                <a href="{{ route('deals.index') }}" class="ml-4 text-gray-600">Natrag</a>
            </div>
        </div>
    </div>
</x-app-layout>
