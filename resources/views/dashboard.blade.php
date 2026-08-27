<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="{{ route('companies.index') }}" class="bg-white shadow rounded p-4 hover:shadow-md transition">
                    <p class="text-sm text-gray-500">Tvrtke</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['companies'] }}</p>
                </a>
                <a href="{{ route('contacts.index') }}" class="bg-white shadow rounded p-4 hover:shadow-md transition">
                    <p class="text-sm text-gray-500">Kontakti</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['contacts'] }}</p>
                </a>
                <a href="{{ route('deals.index') }}" class="bg-white shadow rounded p-4 hover:shadow-md transition">
                    <p class="text-sm text-gray-500">Dealovi</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['deals'] }}</p>
                </a>
                <a href="{{ route('offers.index') }}" class="bg-white shadow rounded p-4 hover:shadow-md transition">
                    <p class="text-sm text-gray-500">Ponude</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['offers'] }}</p>
                </a>
                <div class="bg-indigo-50 border border-indigo-200 shadow rounded p-4">
                    <p class="text-sm text-indigo-600">Korisnici</p>
                    <p class="text-2xl font-bold text-indigo-800">{{ $stats['users'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white shadow rounded p-4">
                    <p class="text-sm text-gray-500">Vrijednost otvorenih dealova</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['open_deals_value'], 2) }} €</p>
                </div>
                <div class="bg-white shadow rounded p-4">
                    <p class="text-sm text-gray-500">Vrijednost dobivenih dealova</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats['won_deals_value'], 2) }} €</p>
                </div>
            </div>

            <div class="bg-white shadow rounded p-6">
                <h3 class="font-semibold mb-3 text-gray-800">Brzi linkovi</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('companies.create') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm">+ Nova tvrtka</a>
                    <a href="{{ route('contacts.create') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm">+ Novi kontakt</a>
                    <a href="{{ route('deals.create') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm">+ Novi deal</a>
                    <a href="{{ route('deals.kanban') }}" class="bg-gray-600 text-white px-3 py-1.5 rounded text-sm">Deal Kanban</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
