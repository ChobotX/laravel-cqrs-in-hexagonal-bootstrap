@php
    use App\Domain\Authorization\AccessScope;

    $existingPermissions = collect($permissions ?? [])->keyBy(fn($p) => (string) $p->permissionKey);
    $scopes = AccessScope::cases();
@endphp

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200"
           aria-label="{{ __('messages.roles.permissions') }}">
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
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($modules as $moduleSlug => $module)
                <tr class="bg-gray-50/30">
                    <td class="px-4 py-2"
                        colspan="5">
                        <div class="flex items-center gap-2">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                       data-module-toggle="{{ $moduleSlug }}"
                                       type="checkbox"
                                       aria-label="{{ $module['label'] }} - toggle all">
                                <span class="text-sm font-semibold text-gray-700">{{ $module['label'] }}</span>
                            </label>
                        </div>
                    </td>
                </tr>
                @foreach ($module['features'] as $featureSlug => $feature)
                    <tr class="transition-colors hover:bg-gray-50/50">
                        <td class="px-4 py-2 pl-10 text-sm text-gray-600">{{ $feature['label'] }}</td>
                        @foreach (['read', 'create', 'update', 'delete'] as $action)
                            @php
                                $permKey = "{$moduleSlug}.{$featureSlug}.{$action}";
                                $hasAction = in_array($action, $feature['actions'], true);
                                $existing = $existingPermissions->get($permKey);
                                $isEnabled = $existing !== null;
                                $currentScope = $existing?->scope->value ?? 'all';
                            @endphp
                            <td class="px-4 py-2 text-center">
                                @if ($hasAction)
                                    <div class="flex flex-col items-center gap-1">
                                        <input class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                               name="permissions[{{ $permKey }}][enabled]"
                                               data-module="{{ $moduleSlug }}"
                                               data-permission="{{ $permKey }}"
                                               type="checkbox"
                                               value="1"
                                               aria-label="{{ $feature['label'] }} - {{ $action }}"
                                               @checked(old("permissions.{$permKey}.enabled", $isEnabled))>
                                        <select class="rounded border-gray-300 px-1 py-0.5 text-xs focus:border-indigo-600 focus:ring-indigo-600"
                                                name="permissions[{{ $permKey }}][scope]"
                                                data-scope-for="{{ $permKey }}"
                                                aria-label="{{ $feature['label'] }} - {{ $action }} {{ __('messages.roles.scope') }}">
                                            @foreach ($scopes as $scope)
                                                <option value="{{ $scope->value }}"
                                                        @selected(old("permissions.{$permKey}.scope", $currentScope) === $scope->value)>
                                                    {{ __("messages.scopes.{$scope->value}") }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <span class="text-gray-300"
                                          aria-hidden="true">&mdash;</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
