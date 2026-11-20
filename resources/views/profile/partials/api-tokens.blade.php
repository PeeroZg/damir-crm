<section class="card overflow-hidden">
    <div class="card-header"><h2 class="card-title flex items-center gap-2">{{ __('API tokens') }} <span class="count">{{ $tokens->count() }}</span></h2></div>
    <div class="p-5">
        <p class="text-base text-ink-600">{{ __('Use a personal token to access the REST API, e.g.') }}</p>
        <code class="mt-2 block overflow-x-auto whitespace-nowrap rounded-lg bg-ink-900 px-3 py-2.5 font-mono text-xs text-ink-200">curl -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/deals') }}</code>

        @if (session('new_token'))
            <div x-data="clipboard(@js(session('new_token')))" class="mt-4 rounded-xl bg-volt-100 p-4 ring-1 ring-inset ring-volt-500/40">
                <p class="text-sm font-semibold text-ink-900">{{ __('Your new token — copy it now, it will not be shown again:') }}</p>
                <div class="mt-2 flex items-center gap-2">
                    <code class="flex-1 break-all rounded-md bg-white px-3 py-2 font-mono text-xs text-ink-900 ring-1 ring-ink-150">{{ session('new_token') }}</code>
                    <x-button variant="primary" size="sm" icon="copy" @click="copy()"><span x-text="copied ? @js(__('Copied!')) : @js(__('Copy'))"></span></x-button>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.tokens.store') }}" class="mt-4 flex flex-wrap gap-2">
            @csrf
            <label for="token-name" class="sr-only">{{ __('Token name') }}</label>
            <input id="token-name" name="name" type="text" required maxlength="100" placeholder="{{ __('Token name, e.g. Zapier') }}" @class(['input max-w-xs', 'input-error' => $errors->has('name')])>
            <x-button type="submit" icon="key">{{ __('Create token') }}</x-button>
        </form>
        @error('name') <p class="error">{{ $message }}</p> @enderror
    </div>

    @if ($tokens->isNotEmpty())
        <div class="overflow-x-auto border-t border-ink-100">
            <table class="table">
                <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Created') }}</th><th>{{ __('Last used') }}</th><th><span class="sr-only">{{ __('Revoke') }}</span></th></tr></thead>
                <tbody>
                    @foreach ($tokens as $token)
                        <tr>
                            <td class="font-medium text-ink-900"><span class="inline-flex items-center gap-2"><x-icon name="key" class="h-4 w-4 text-ink-400" />{{ $token->name }}</span></td>
                            <td class="tnum">{{ format_date($token->created_at) }}</td>
                            <td class="text-ink-500">{{ $token->last_used_at?->diffForHumans() ?? __('Never') }}</td>
                            <td class="text-right"><x-delete-button :action="route('profile.tokens.destroy', $token->id)" :label="__('Revoke')" :confirm="__('Revoke this token? Apps using it will stop working.')" size="sm" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
