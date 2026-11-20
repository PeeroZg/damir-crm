<x-layouts.guest :title="__('Log in')">
    <div x-data="{ email: @js(old('email', '')), password: '', fill(e) { this.email = e; this.password = @js(config('crm.demo.enabled') ? config('crm.demo.password') : ''); this.$nextTick(() => this.$refs.form.requestSubmit()) } }">
        <h2 class="font-display text-4xl font-bold text-ink-900">{{ __('Welcome back') }}</h2>
        <p class="mt-2 text-md text-ink-500">{{ __('Sign in to your sales workspace.') }}</p>

        @if (config('crm.demo.enabled'))
            @php $domain = config('crm.demo.domain'); @endphp
            <div class="mt-8 rounded-2xl bg-ink-50 p-1.5 ring-1 ring-inset ring-ink-150">
                <div class="flex items-center justify-between px-3 pb-2 pt-2">
                    <p class="flex items-center gap-2 text-sm font-semibold text-ink-900"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-volt-400"><x-icon name="bolt" class="h-3.5 w-3.5" /></span>{{ __('Try the demo') }}</p>
                    <span class="text-xs text-ink-500">{{ __('One click') }}</span>
                </div>
                <p class="px-3 pb-3 text-sm text-ink-600">{{ __('Pick a role — the form fills itself in. Both see the same pipeline, but permissions differ.') }}</p>
                <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                    @foreach ([['admin', 'Damir Peer', __('Administrator'), __('manages team, edits everything')], ['ana', 'Ana Kovač', __('Sales rep'), __('edits own deals & offers')]] as [$login, $person, $role, $desc])
                        <button type="button" class="group flex items-start gap-3 rounded-xl bg-white p-3 text-left shadow-card ring-1 ring-ink-150 transition hover:-translate-y-px hover:ring-ink-900" @click="fill(@js($login.'@'.$domain))">
                            <x-avatar :name="$person" size="lg" class="ring-0" />
                            <span class="min-w-0 flex-1"><span class="flex items-center gap-1 text-base font-semibold text-ink-900">{{ $role }}<x-icon name="arrow-right" class="h-3.5 w-3.5 -translate-x-1 opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100" /></span><span class="mt-0.5 block text-xs leading-4 text-ink-500">{{ $desc }}</span></span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="my-7 flex items-center gap-3 text-xs text-ink-500"><span class="h-px flex-1 bg-ink-150"></span>{{ __('or sign in with e-mail') }}<span class="h-px flex-1 bg-ink-150"></span></div>
        @else
            <div class="mt-8"></div>
        @endif

        <form method="POST" action="{{ route('login') }}" x-ref="form" class="space-y-4">
            @csrf
            <x-field :label="__('Email')" name="email" required>
                <input id="email" name="email" type="email" x-model="email" required autofocus autocomplete="username" @class(['input h-11', 'input-error' => $errors->has('email')])>
            </x-field>
            <x-field :label="__('Password')" name="password" required>
                <input id="password" name="password" type="password" x-model="password" required autocomplete="current-password" class="input h-11">
            </x-field>
            <label class="flex items-center gap-2.5 text-base text-ink-700"><input type="checkbox" name="remember" class="checkbox">{{ __('Remember me') }}</label>
            <x-button type="submit" size="lg" icon-right="arrow-right" class="w-full">{{ __('Log in') }}</x-button>
        </form>
    </div>
</x-layouts.guest>
