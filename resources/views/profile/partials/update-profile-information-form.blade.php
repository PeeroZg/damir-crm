<section class="card">
    <div class="card-header"><h2 class="card-title">{{ __('Account') }}</h2><x-status :value="$user->role" /></div>
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 p-5">
        @csrf
        @method('PATCH')
        <x-field :label="__('Name')" name="name" required>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required @class(['input', 'input-error' => $errors->has('name')])>
        </x-field>
        <x-field :label="__('Email')" name="email" required :hint="$user->isDemoAccount() ? __('Demo accounts keep their e-mail so everyone can log in.') : null">
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required @class(['input', 'input-error' => $errors->has('email')]) @readonly($user->isDemoAccount())>
        </x-field>
        <x-field :label="__('Language')" name="locale" required>
            <select id="locale" name="locale" class="select">
                @foreach (config('crm.locales') as $code => $label)
                    <option value="{{ $code }}" @selected(old('locale', $user->locale) === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </x-field>
        <div class="flex justify-end pt-1"><x-button type="submit" icon="check">{{ __('Save') }}</x-button></div>
    </form>
</section>
