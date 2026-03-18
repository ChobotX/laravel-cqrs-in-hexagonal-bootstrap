@php
    $existing = $existingPermissions->get($permKey);
    $isEnabled = $existing !== null;
    $currentScope = $existing?->scope->value ?? 'all';
@endphp

@if ($isEnabled)
    @include('components.permission-matrix.badge', [
        'variant' => 'green',
        'title' => "{$feature['label']} - {$action}",
        'content' => __("messages.scopes.{$currentScope}"),
    ])
@else
    <span class="text-gray-300"
          aria-hidden="true">&mdash;</span>
@endif
