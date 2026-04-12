@if (empty($changes))
    <span class="text-xs text-gray-400">{{ __('messages.audit_log.no_changes') }}</span>
@else
    <ul class="space-y-1 text-xs text-gray-600">
        @foreach ($changes as $change)
            <li class="font-mono">
                <span class="font-semibold text-gray-700">{{ $change['property'] }}:</span>
                @if (!empty($change['sensitive']))
                    <span class="italic text-gray-400">••• ({{ __('messages.audit_log.redacted') }})</span>
                @else
                    <span
                          class="text-red-700">{{ $change['old'] === null ? '∅' : json_encode($change['old'], JSON_UNESCAPED_UNICODE) }}</span>
                    <span class="text-gray-400">→</span>
                    <span
                          class="text-green-700">{{ $change['new'] === null ? '∅' : json_encode($change['new'], JSON_UNESCAPED_UNICODE) }}</span>
                @endif
            </li>
        @endforeach
    </ul>
@endif
