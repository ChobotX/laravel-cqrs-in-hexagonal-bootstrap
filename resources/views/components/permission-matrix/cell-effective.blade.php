@php
    $ep = $effectiveByKey->get($permKey);
@endphp

@if ($ep !== null && $ep->granted && str_starts_with($ep->source, 'role:'))
    @include('components.permission-matrix.badge', [
        'variant' => 'green',
        'title' => __('messages.permissions.granted') . ': ' . $ep->source,
        'content' => __("messages.scopes.{$ep->scope->value}"),
    ])
@elseif ($ep !== null && $ep->granted && str_starts_with($ep->source, 'override:'))
    @include('components.permission-matrix.badge', [
        'variant' => 'blue',
        'title' => __('messages.permissions.granted') . ': ' . $ep->source,
        'content' => __("messages.scopes.{$ep->scope->value}"),
    ])
@elseif ($ep !== null && !$ep->granted)
    @include('components.permission-matrix.badge', [
        'variant' => 'red',
        'title' => __('messages.permissions.deny_tooltip', ['source' => $ep->source]),
        'content' => __('messages.permissions.denied'),
        'strikethrough' => true,
    ])
@else
    <span class="text-gray-300"
          aria-hidden="true">&mdash;</span>
@endif
