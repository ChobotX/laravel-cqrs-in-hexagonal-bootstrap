@extends('layouts.app')

@section('title', __('messages.permissions.title'))

@section('content')
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h2>
        <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.permissions.subtitle', ['name' => $user->name]) }}</p>
    </div>

    {{-- Assigned Roles --}}
    <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-medium text-gray-900">{{ __('messages.permissions.assigned_roles') }}</h3>
            @hasPermission('users.roles.update')
                <form class="flex items-center gap-2"
                      id="assign-role-form"
                      method="POST"
                      action="{{ route('users.permissions', $user->id) }}">
                    @csrf
                    <input name="_action"
                           type="hidden"
                           value="assign_role">
                    <label class="sr-only"
                           for="assign-role-select">{{ __('messages.permissions.add_role') }}</label>
                    <select class="rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-indigo-600 focus:ring-indigo-600"
                            id="assign-role-select"
                            name="role_id">
                        @php
                            $assignedRoleIds = collect($userRoles)->map(fn($r) => (string) $r->id)->all();
                        @endphp
                        @foreach ($allRoles as $availableRole)
                            @unless (in_array((string) $availableRole->id, $assignedRoleIds, true))
                                <option value="{{ $availableRole->id }}">{{ $availableRole->name }}</option>
                            @endunless
                        @endforeach
                    </select>
                    <x-primary-button skip-permission
                                      :label="__('messages.permissions.add_role')" />
                </form>
            @endhasPermission
        </div>
        <div class="px-6 py-4">
            @if (count($userRoles) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach ($userRoles as $userRole)
                        <div
                             class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 ring-1 ring-indigo-700/10">
                            <span>{{ $userRole->name }}</span>
                            @hasPermission('users.roles.update')
                                <form class="inline"
                                      method="POST"
                                      action="{{ route('users.permissions', $user->id) }}">
                                    @csrf
                                    <input name="_action"
                                           type="hidden"
                                           value="revoke_role">
                                    <input name="role_id"
                                           type="hidden"
                                           value="{{ $userRole->id }}">
                                    <x-icon-button class="text-indigo-400 transition-colors hover:text-indigo-700"
                                                   skip-permission
                                                   icon="heroicon-o-x-mark"
                                                   :label="__('messages.permissions.remove_role') .
                                                       ' ' .
                                                       $userRole->name" />
                                </form>
                            @endhasPermission
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('messages.permissions.not_granted') }}</p>
            @endif
        </div>
    </div>

    {{-- Effective Permissions --}}
    <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-medium text-gray-900">{{ __('messages.permissions.effective_permissions') }}</h3>
        </div>
        <div class="overflow-x-auto">
            @php
                $effectiveByKey = collect($effectivePermissions)->keyBy(fn($ep) => (string) $ep->permissionKey);
            @endphp
            <table class="min-w-full divide-y divide-gray-200"
                   aria-label="{{ __('messages.permissions.effective_permissions') }}">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.module') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.read') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.create_perm') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.update_perm') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.delete_perm') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.permissions.source') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($modules as $moduleSlug => $module)
                        <tr class="bg-gray-50/30">
                            <td class="px-4 py-2"
                                colspan="6">
                                <span class="text-sm font-semibold text-gray-700">{{ $module['label'] }}</span>
                            </td>
                        </tr>
                        @foreach ($module['features'] as $featureSlug => $feature)
                            <tr class="transition-colors hover:bg-gray-50/50">
                                <td class="px-4 py-2 pl-10 text-sm text-gray-600">{{ $feature['label'] }}</td>
                                @foreach (['read', 'create', 'update', 'delete'] as $action)
                                    @php
                                        $permKey = "{$moduleSlug}.{$featureSlug}.{$action}";
                                        $hasAction = in_array($action, $feature['actions'], true);
                                        $ep = $effectiveByKey->get($permKey);
                                    @endphp
                                    <td class="px-4 py-2 text-center">
                                        @if ($hasAction && $ep !== null)
                                            @if ($ep->granted && str_starts_with($ep->source, 'role:'))
                                                <span class="inline-flex items-center rounded bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700"
                                                      title="{{ __('messages.permissions.granted') }}: {{ $ep->source }}">
                                                    {{ __("messages.scopes.{$ep->scope->value}") }}
                                                </span>
                                            @elseif ($ep->granted && str_starts_with($ep->source, 'override:'))
                                                <span class="inline-flex items-center rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700"
                                                      title="{{ __('messages.permissions.granted') }}: {{ $ep->source }}">
                                                    {{ __("messages.scopes.{$ep->scope->value}") }}
                                                </span>
                                            @elseif (!$ep->granted)
                                                <span class="inline-flex items-center rounded bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 line-through"
                                                      title="{{ __('messages.permissions.deny_tooltip', ['source' => $ep->source]) }}">
                                                    {{ __('messages.permissions.denied') }}
                                                </span>
                                            @endif
                                        @elseif ($hasAction)
                                            <span class="text-gray-300">&mdash;</span>
                                        @else
                                            <span class="text-gray-300"
                                                  aria-hidden="true">&mdash;</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-2 text-xs text-gray-500">
                                    @php
                                        $sources = [];
                                        foreach (['read', 'create', 'update', 'delete'] as $a) {
                                            $k = "{$moduleSlug}.{$featureSlug}.{$a}";
                                            $e = $effectiveByKey->get($k);
                                            if (
                                                $e !== null &&
                                                $e->source !== '' &&
                                                !in_array($e->source, $sources, true)
                                            ) {
                                                $sources[] = $e->source;
                                            }
                                        }
                                    @endphp
                                    {{ implode(', ', $sources) }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Override Controls --}}
    @hasPermission('users.roles.update')
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-medium text-gray-900">{{ __('messages.permissions.overrides') }}</h3>
            </div>
            <div class="p-6">
                <form class="flex flex-wrap items-end gap-3"
                      method="POST"
                      action="{{ route('users.permissions', $user->id) }}">
                    @csrf
                    <input name="_action"
                           type="hidden"
                           value="set_override">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700"
                               for="override-permission">{{ __('messages.roles.permissions') }}</label>
                        <select class="rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-indigo-600 focus:ring-indigo-600"
                                id="override-permission"
                                name="permission">
                            @foreach ($modules as $moduleSlug => $module)
                                @foreach ($module['features'] as $featureSlug => $feature)
                                    @foreach ($feature['actions'] as $action)
                                        <option value="{{ $moduleSlug }}.{{ $featureSlug }}.{{ $action }}">
                                            {{ $module['label'] }} / {{ $feature['label'] }} / {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700"
                               for="override-type">Type</label>
                        <select class="rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-indigo-600 focus:ring-indigo-600"
                                id="override-type"
                                name="type">
                            <option value="grant">{{ __('messages.permissions.granted') }}</option>
                            <option value="deny">{{ __('messages.permissions.denied') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700"
                               for="override-scope">{{ __('messages.permissions.scope') }}</label>
                        <select class="rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-indigo-600 focus:ring-indigo-600"
                                id="override-scope"
                                name="scope">
                            @foreach (\App\Domain\Authorization\AccessScope::cases() as $scope)
                                <option value="{{ $scope->value }}">{{ __("messages.scopes.{$scope->value}") }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-primary-button skip-permission
                                      :label="__('messages.permissions.add_override')" />
                </form>
            </div>
        </div>
    @endhasPermission
@endsection
