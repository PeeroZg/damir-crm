<x-card :title="__('Password')">
    @if ($user->isDemoAccount())
        <x-empty-state icon="key" :title="__('Password changes are disabled')" :description="__('This is a shared demo account. Create your own user on the Team page (as admin) to try it.')" />
    @else
        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="form-label">{{ __('Current password') }}</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="form-input">
                @error('current_password', 'updatePassword') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="form-label">{{ __('New password') }}</label>
                <input id="password" name="password" type="password" autocomplete="new-password" class="form-input">
                @error('password', 'updatePassword') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="form-input">
            </div>

            <div class="flex justify-end pt-2">
                <x-button type="submit">{{ __('Update password') }}</x-button>
            </div>
        </form>
    @endif
</x-card>
