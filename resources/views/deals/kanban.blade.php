<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dealovi — Kanban</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('deals.index') }}" class="text-blue-600 underline">Lista prikaz</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                @php
                    $statuses = [
                        'lead' => 'Lead',
                        'kvalificiran' => 'Kvalificiran',
                        'ponuda_poslana' => 'Ponuda poslana',
                        'pregovori' => 'Pregovori',
                        'dobiven' => 'Dobiven',
                        'izgubljen' => 'Izgubljen',
                    ];
                @endphp

                @foreach($statuses as $key => $label)
                    <div class="bg-gray-100 rounded p-3">
                        <h3 class="font-semibold mb-3">{{ $label }}</h3>

                        @foreach($deals->where('status', $key) as $deal)
                            <div class="bg-white rounded shadow p-3 mb-3">
                                <p class="font-medium">{{ $deal->title }}</p>
                                <p class="text-sm text-gray-500">{{ $deal->company->name }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($deal->value, 2) }} €</p>

                                <form method="POST" action="{{ route('deals.updateStatus', $deal) }}" class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="w-full border rounded p-1 text-sm">
                                        @foreach($statuses as $optKey => $optLabel)
                                            <option value="{{ $optKey }}" @selected($deal->status === $optKey)>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                </form>

                                <a href="{{ route('deals.show', $deal) }}" class="text-xs text-blue-600 mt-2 inline-block">Detalji</a>
                                <a href="{{ route('offers.create', ['deal_id' => $deal->id]) }}" class="text-xs text-green-600 mt-2 ml-2 inline-block">+ Ponuda</a>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
