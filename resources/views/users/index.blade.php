<x-layouts.app :title="__('Team')">
<div class="mx-auto max-w-[1280px]">
    <x-page-header :title="__('Team')">
        <x-slot:description>{{ __('Sales reps can edit their own deals and offers; admins can edit everything.') }}</x-slot:description>
        <x-slot:actions><x-button icon="plus" :href="route('users.create')">{{ __('New user') }}</x-button></x-slot:actions>
    </x-page-header>

    <section class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table min-w-[760px]">
                <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Role') }}</th><th class="text-right">{{ __('Open deals') }}</th><th class="text-right">{{ __('Won') }}</th><th>{{ __('Member since') }}</th><th><span class="sr-only">{{ __('Edit') }}</span></th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$user->name" size="lg" class="ring-0" />
                                    <div class="min-w-0">
                                        <p class="font-medium text-ink-900">{{ $user->name }} @if ($user->is(auth()->user()))<span class="text-xs font-normal text-ink-500">({{ __('you') }})</span>@endif</p>
                                        <p class="text-xs text-ink-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><x-status :value="$user->role" /></td>
                            <td class="text-right tnum">{{ $user->open_deals_count }}</td>
                            <td class="text-right font-semibold tnum">{{ money($user->won_value ?? 0) }}</td>
                            <td class="tnum text-ink-500">{{ format_date($user->created_at) }}</td>
                            <td class="text-right">
                                @can('update', $user)
                                    <x-button size="sm" variant="ghost" icon="pencil" :href="route('users.edit', $user)">{{ __('Edit') }}</x-button>
                                @else
                                    <span class="text-xs text-ink-500" title="{{ __('Demo accounts are protected.') }}">{{ __('Protected') }}</span>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
</x-layouts.app>
