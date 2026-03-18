@php
    $existing = $existingPermissions->get($permKey);
    $isEnabled = $existing !== null;
    $currentScope = $existing?->scope->value ?? 'all';
    $scopes = \App\Domain\Authorization\AccessScope::cases();
@endphp

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
