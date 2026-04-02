@extends('layouts.app')

@section('title', __('messages.users.title'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <span
              class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10">
            {{ $result->total }} {{ trans_choice('messages.users.count', $result->total) }}
        </span>
        <x-primary-button permission="users.list.create"
                          :href="route('users.create')"
                          :label="__('messages.users.create_action')" />
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.users.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <x-sortable-header column="name"
                                           :label="__('messages.users.user')"
                                           :sorting="$sorting" />
                        <x-sortable-header column="email"
                                           :label="__('messages.users.email')"
                                           :sorting="$sorting" />
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.users.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($result->items as $user)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($user->avatarFileId !== null)
                                        <img class="h-9 w-9 shrink-0 rounded-full object-cover"
                                             src="{{ route('files.show', $user->avatarFileId->value) }}"
                                             alt="{{ $user->name->value }}">
                                    @else
                                        <div
                                             class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">
                                            {{ strtoupper(substr($user->name->value, 0, 1)) }}{{ strtoupper(substr($user->name->value, strpos($user->name->value, ' ') + 1, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span
                                                  class="text-base font-medium text-gray-900 sm:text-sm">{{ $user->name->value }}</span>
                                            @if ($canReadLabels)
                                                <x-badge-list :labels="$userLabels[$user->id->value] ?? []" />
                                            @endif
                                        </div>
                                        @if ($canReadRoles || $canReadTeams)
                                            <p class="mt-0.5 text-xs text-gray-400">
                                                @if ($canReadRoles)
                                                    @forelse ($userRoles[$user->id->value] ?? [] as $role)
                                                        <a class="transition-colors hover:text-indigo-600"
                                                           data-tooltip="{{ $role->name->value }}"
                                                           href="{{ route('roles.show', $role->id->value) }}">{{ $role->name->value }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @empty
                                                        <span
                                                              class="text-gray-300">{{ __('messages.users.no_role') }}</span>
                                                    @endforelse
                                                @endif
                                                @if ($canReadTeams && !empty($userTeams[$user->id->value] ?? []))
                                                    <span class="text-gray-300">{{ __('messages.users.in_team') }}</span>
                                                    @foreach ($userTeams[$user->id->value] as $team)
                                                        <a class="transition-colors hover:text-indigo-600"
                                                           data-tooltip="{{ $team->name }}"
                                                           href="{{ route('teams.show', $team->id) }}">{{ $team->name }}</a>
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-base text-gray-500 sm:text-sm">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($isSuperAdmin && $user->id->value !== $currentUserId)
                                        <x-action-button skip-permission
                                                         :action="route('impersonation.start', $user->id)"
                                                         icon="heroicon-o-finger-print"
                                                         :label="__('messages.impersonation.start') .
                                                             ' ' .
                                                             $user->name->value" />
                                    @endif
                                    <x-action-button permission="users.list.update"
                                                     :href="route('users.edit', $user->id)"
                                                     icon="heroicon-o-pencil-square"
                                                     :label="__('messages.users.edit_action') .
                                                         ' ' .
                                                         $user->name->value" />
                                    <x-action-button permission="users.list.delete"
                                                     :action="route('users.destroy', $user->id)"
                                                     method="DELETE"
                                                     icon="heroicon-o-trash"
                                                     :label="__('messages.users.delete_action') .
                                                         ' ' .
                                                         $user->name->value"
                                                     variant="danger"
                                                     confirm
                                                     :confirm-title="__('messages.users.delete_confirm_title')"
                                                     :confirm-message="__('messages.users.delete_confirm_message', [
                                                         'name' => $user->name->value,
                                                     ])" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table-empty-state colspan="3"
                                             :message="__('messages.users.empty')" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :result="$result" />
@endsection
