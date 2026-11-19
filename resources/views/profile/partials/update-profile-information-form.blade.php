<x-card :title="__('Account')">
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <x-field :label="__('Name')" name="name" required>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="form-input">
        </x-field>

        <x-field :label="__('Email')" name="email" required :hint="$user->isDemoAccount() ? __('Demo accounts keep their e-mail so everyone can log in.') : null">
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="form-input" @readonly($user->isDemoAccount())>
        </x-field>

        <x-field :label="__('Language')" name="locale" required>
            <select id="locale" name="locale" class="form-input">
                @foreach (config('crm.locales') as $code => $label)
                    <option value="{{ $code }}" @selected(old('locale', $user->locale) === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </x-field>

        <div class="flex items-center justify-between pt-2">
            <x-badge :color="$user->role->color()">{{ $user->role->label() }}</x-badge>
            <x-button type="submit">{{ __('Save') }}</x-button>
        </div>
    </form>
</x-card>
