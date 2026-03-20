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
                      action="{{ route('users.permissions.manage', $user->id) }}">
                    @csrf
                    <input name="_action"
                           type="hidden"
                           value="assign_role">
                    <div data-autocomplete
                         data-search-url="{{ route('internal-api.roles.search') }}"
                         data-exclude-ids="{{ json_encode(collect($userRoles)->map(fn($r) => (string) $r->id)->all()) }}"
                         data-input-name="role_id"
                         data-placeholder="{{ __('messages.users.roles_search') }}"
                         data-no-results-text="{{ __('messages.organizations.no_results') }}">
                    </div>
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
                                      action="{{ route('users.permissions.manage', $user->id) }}">
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
        @include('components.permission-matrix', [
            'modules' => $modules,
            'effectivePermissions' => $effectivePermissions,
            'mode' => 'effective',
        ])
    </div>

    {{-- Active Overrides --}}
    @if (count($userOverrides) > 0)
        <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-medium text-gray-900">{{ __('messages.permissions.active_overrides') }}</h3>
            </div>
            <div class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                    @foreach ($userOverrides as $override)
                        <div
                             class="{{ $override->type->value === 'grant' ? 'bg-green-50 text-green-700 ring-green-700/10' : 'bg-red-50 text-red-700 ring-red-700/10' }} inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1">
                            <span>{{ $override->permissionKey }} ({{ $override->scope->value }})</span>
                            @hasPermission('users.roles.update')
                                <form class="inline"
                                      method="POST"
                                      action="{{ route('users.permissions.manage', $user->id) }}">
                                    @csrf
                                    <input name="_action"
                                           type="hidden"
                                           value="remove_override">
                                    <input name="permission"
                                           type="hidden"
                                           value="{{ $override->permissionKey }}">
                                    <x-icon-button class="{{ $override->type->value === 'grant' ? 'text-green-400 hover:text-green-700' : 'text-red-400 hover:text-red-700' }} transition-colors"
                                                   skip-permission
                                                   icon="heroicon-o-x-mark"
                                                   :label="__('messages.permissions.remove_override') .
                                                       ' ' .
                                                       $override->permissionKey" />
                                </form>
                            @endhasPermission
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Override Controls --}}
    @hasPermission('users.roles.update')
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-medium text-gray-900">{{ __('messages.permissions.overrides') }}</h3>
            </div>
            <div class="p-6">
                <form class="flex flex-wrap items-end gap-3"
                      method="POST"
                      action="{{ route('users.permissions.manage', $user->id) }}">
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
