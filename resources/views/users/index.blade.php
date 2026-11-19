<x-layouts.app :title="__('Team')">
    <x-page-header :title="__('Team')" :subtitle="__('Sales reps can edit their own deals and offers; admins can edit everything.')">
        <x-slot:actions>
            <x-button icon="plus" :href="route('users.create')">{{ __('New user') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/60">
                    <tr>
                        <th class="table-th">{{ __('Name') }}</th>
                        <th class="table-th">{{ __('Role') }}</th>
                        <th class="table-th text-right">{{ __('Open deals') }}</th>
                        <th class="table-th text-right">{{ __('Won') }}</th>
                        <th class="table-th">{{ __('Member since') }}</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-slate-50/60">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$user->name" />
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $user->name }} @if ($user->is(auth()->user()))<span class="text-xs text-slate-400">({{ __('you') }})</span>@endif</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td"><x-status :value="$user->role" /></td>
                            <td class="table-td text-right">{{ $user->open_deals_count }}</td>
                            <td class="table-td text-right font-medium">{{ money($user->won_value ?? 0) }}</td>
                            <td class="table-td text-slate-500">{{ format_date($user->created_at) }}</td>
                            <td class="table-td text-right">
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="link text-xs">{{ __('Edit') }}</a>
                                @else
                                    <span class="text-xs text-slate-400" title="{{ __('Demo accounts are protected.') }}">{{ __('Protected') }}</span>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
