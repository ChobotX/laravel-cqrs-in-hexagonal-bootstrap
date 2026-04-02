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
                        @if ($canManageOverrides)
                            <form class="inline"
                                  method="POST"
                                  action="{{ route('users.permissions.manage', $userId) }}">
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
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Override Controls --}}
@if ($canManageOverrides)
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-medium text-gray-900">{{ __('messages.permissions.overrides') }}</h3>
        </div>
        <div class="p-6">
            <form class="flex flex-wrap items-end gap-3"
                  method="POST"
                  action="{{ route('users.permissions.manage', $userId) }}">
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
                        @foreach ($accessScopes as $scope)
                            <option value="{{ $scope->value }}">{{ __("messages.scopes.{$scope->value}") }}</option>
                        @endforeach
                    </select>
                </div>

                <x-primary-button skip-permission
                                  :label="__('messages.permissions.add_override')" />
            </form>
        </div>
    </div>
@endif
