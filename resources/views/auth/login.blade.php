<x-layouts.guest :title="__('Log in')">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('Welcome back') }}</h1>
    <p class="mt-1 text-sm text-slate-500">{{ __('Sign in to your sales workspace.') }}</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data="{ email: @js(old('email', '')), password: '' }" x-ref="form">
        @csrf

        <x-field :label="__('Email')" name="email" required>
            <input id="email" name="email" type="email" x-model="email" required autofocus autocomplete="username" class="form-input">
        </x-field>

        <x-field :label="__('Password')" name="password" required>
            <input id="password" name="password" type="password" x-model="password" required autocomplete="current-password" class="form-input">
        </x-field>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            {{ __('Remember me') }}
        </label>

        <x-button type="submit" class="w-full">{{ __('Log in') }}</x-button>

        @if (config('crm.demo.enabled'))
            <div class="rounded-xl border border-brand-100 bg-brand-50/60 p-4">
                <p class="text-sm font-semibold text-brand-900">{{ __('Try the demo') }}</p>
                <p class="mt-0.5 text-xs text-brand-800/80">{{ __('Pick a role — the form fills itself in. Both see the same pipeline, but permissions differ.') }}</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    @foreach ([['admin', __('Administrator'), __('manages team, edits everything')], ['ana', __('Sales rep'), __('edits own deals & offers')]] as [$login, $role, $desc])
                        <button type="button"
                                @click="email = @js($login.'@'.config('crm.demo.domain')); password = @js(config('crm.demo.password')); $nextTick(() => $refs.form.requestSubmit())"
                                class="rounded-lg bg-white p-2.5 text-left shadow-sm ring-1 ring-brand-100 transition hover:ring-brand-300">
                            <span class="block text-sm font-semibold text-slate-900">{{ $role }}</span>
                            <span class="block text-xs text-slate-500">{{ $desc }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </form>
</x-layouts.guest>
