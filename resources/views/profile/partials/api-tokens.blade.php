<x-card :title="__('API tokens')">
    <p class="text-sm text-slate-600">
        {{ __('Use a personal token to access the REST API, e.g.') }}
        <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">curl -H "Authorization: Bearer &lt;token&gt;" {{ url('/api/v1/deals') }}</code>
    </p>

    @if (session('new_token'))
        <div x-data="clipboard(@js(session('new_token')))" class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm font-semibold text-emerald-900">{{ __('Your new token — copy it now, it will not be shown again:') }}</p>
            <div class="mt-2 flex items-center gap-2">
                <code class="flex-1 break-all rounded bg-white px-3 py-2 font-mono text-xs text-slate-800 ring-1 ring-emerald-200">{{ session('new_token') }}</code>
                <x-button variant="secondary" size="sm" icon="copy" @click="copy()"><span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span></x-button>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.tokens.store') }}" class="mt-4 flex gap-2">
        @csrf
        <input name="name" type="text" required maxlength="100" placeholder="{{ __('Token name, e.g. Zapier') }}" class="form-input max-w-xs" aria-label="{{ __('Token name') }}">
        <x-button type="submit" icon="key">{{ __('Create token') }}</x-button>
    </form>
    @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

    @if ($tokens->isNotEmpty())
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead><tr>
                    <th class="table-th">{{ __('Name') }}</th>
                    <th class="table-th">{{ __('Created') }}</th>
                    <th class="table-th">{{ __('Last used') }}</th>
                    <th class="table-th"></th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($tokens as $token)
                        <tr>
                            <td class="table-td font-medium">{{ $token->name }}</td>
                            <td class="table-td">{{ format_date($token->created_at) }}</td>
                            <td class="table-td">{{ $token->last_used_at?->diffForHumans() ?? __('Never') }}</td>
                            <td class="table-td text-right">
                                <x-delete-button :action="route('profile.tokens.destroy', $token->id)" :label="__('Revoke')" :confirm="__('Revoke this token? Apps using it will stop working.')" size="sm" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-card>
