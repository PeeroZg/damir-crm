<div class="grid gap-5 sm:grid-cols-2">
    <x-field :label="__('Name')" name="name" required>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" class="form-input" autofocus>
    </x-field>
    <x-field :label="__('Email')" name="email" required>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="form-input">
    </x-field>
    <x-field :label="__('Role')" name="role" required :hint="$user->is(auth()->user()) ? __('You cannot change your own role.') : null">
        <select id="role" name="role" class="form-input" @disabled($user->is(auth()->user()))>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
        @if ($user->is(auth()->user()))<input type="hidden" name="role" value="{{ $user->role->value }}">@endif
    </x-field>
    <x-field :label="$user->exists ? __('New password') : __('Password')" name="password" :required="! $user->exists" :hint="$user->exists ? __('Leave empty to keep the current password.') : __('At least 8 characters.')">
        <input id="password" name="password" type="password" autocomplete="new-password" class="form-input" @required(! $user->exists)>
    </x-field>
</div>
