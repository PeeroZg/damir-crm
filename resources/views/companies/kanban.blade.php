<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Tvrtke — Kanban</h2>
            <a href="{{ route('companies.index') }}" class="text-blue-600">Tablični prikaz</a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-3 gap-4">
            @foreach (['lead' => 'Lead', 'aktivan' => 'Aktivan', 'neaktivan' => 'Neaktivan'] as $key => $label)
                <div class="bg-gray-100 rounded-lg p-3 min-h-[300px]">
                    <h3 class="font-semibold mb-3">{{ $label }}</h3>
                    <div class="space-y-2">
                        @foreach ($companies->where('status', $key) as $company)
                            <div class="bg-white shadow rounded p-3">
                                <a href="{{ route('companies.show', $company) }}" class="font-medium text-blue-600">{{ $company->name }}</a>
                                <div class="text-sm text-gray-500 mb-2">{{ $company->email }}</div>
                                <form method="POST" action="{{ route('companies.updateStatus', $company) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="w-full border rounded p-1 text-sm">
                                        <option value="lead" {{ $company->status == 'lead' ? 'selected' : '' }}>Lead</option>
                                        <option value="aktivan" {{ $company->status == 'aktivan' ? 'selected' : '' }}>Aktivan</option>
                                        <option value="neaktivan" {{ $company->status == 'neaktivan' ? 'selected' : '' }}>Neaktivan</option>
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
