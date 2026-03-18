@php
    $mode = $mode ?? 'edit';
    $colCount = $mode === 'effective' ? 6 : 5;

    if ($mode === 'effective') {
        $effectiveByKey = collect($effectivePermissions)->keyBy(fn($ep) => (string) $ep->permissionKey);
    } else {
        $existingPermissions = collect($permissions ?? [])->keyBy(fn($p) => (string) $p->permissionKey);
    }
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
                @if ($mode === 'effective')
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                        scope="col">{{ __('messages.permissions.source') }}</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($modules as $moduleSlug => $module)
                <tr class="bg-gray-50/30">
                    <td class="px-4 py-2"
                        colspan="{{ $colCount }}">
                        <div class="flex items-center gap-2">
                            @if ($mode === 'edit')
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                           data-module-toggle="{{ $moduleSlug }}"
                                           type="checkbox"
                                           aria-label="{{ $module['label'] }} - toggle all">
                                    <span class="text-sm font-semibold text-gray-700">{{ $module['label'] }}</span>
                                </label>
                            @else
                                <span class="text-sm font-semibold text-gray-700">{{ $module['label'] }}</span>
                            @endif
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
                            @endphp
                            <td class="px-4 py-2 text-center">
                                @if ($hasAction)
                                    @include("components.permission-matrix.cell-{$mode}")
                                @else
                                    <span class="text-gray-300"
                                          aria-hidden="true">&mdash;</span>
                                @endif
                            </td>
                        @endforeach
                        @if ($mode === 'effective')
                            <td class="px-4 py-2 text-xs text-gray-500">
                                {{ collect(['read', 'create', 'update', 'delete'])->map(fn($a) => $effectiveByKey->get("{$moduleSlug}.{$featureSlug}.{$a}")?->source)->filter()->unique()->implode(', ') }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
