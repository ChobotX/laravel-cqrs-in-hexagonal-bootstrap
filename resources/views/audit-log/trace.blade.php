@extends('layouts.app')

@section('title', __('messages.audit_log.trace_title', ['traceId' => Str::limit($traceId, 12, '...')]))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('messages.audit_log.trace_title', ['traceId' => $traceId]) }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ count($entries) }} {{ trans_choice('messages.audit_log.grouped_entries', count($entries)) }}
            </p>
        </div>
        <a class="cursor-pointer rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-200"
           href="{{ route('audit-log.index') }}"
           title="{{ __('messages.audit_log.back') }}">{{ __('messages.audit_log.back') }}</a>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.audit_log.trace_title', ['traceId' => $traceId]) }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.occurred_at') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.action') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.entity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.ip_address') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.payload') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($entries as $entry)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                <time data-local-datetime
                                      datetime="{{ $entry->occurredAt->format(DATE_ATOM) }}">{{ $entry->occurredAt->format('Y-m-d H:i:s') }}</time>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $entry->actionLabel }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($entry->entityType)
                                    <span
                                          class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-500/10">
                                        {{ $entry->entityType }}
                                    </span>
                                    <span
                                          class="ml-1 font-mono text-xs text-gray-400">{{ Str::limit($entry->entityId ?? '', 8, '...') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($entry->status->value === 'success')
                                    <span
                                          class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-600/10">
                                        {{ __('messages.audit_log.success') }}
                                    </span>
                                @else
                                    <span
                                          class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-red-600/10">
                                        {{ __('messages.audit_log.failure') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $entry->ipAddress ?? '-' }}
                            </td>
                            <td class="max-w-xs px-6 py-4">
                                <div class="mb-2">
                                    <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ __('messages.audit_log.changes') }}</div>
                                    @include('audit-log._changes', ['changes' => $entry->changes])
                                </div>
                                <details class="mt-2">
                                    <summary
                                             class="cursor-pointer text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ __('messages.audit_log.payload') }}</summary>
                                    <pre class="mt-1 overflow-x-auto whitespace-pre-wrap rounded bg-gray-50 p-2 font-mono text-xs text-gray-600">{{ json_encode($entry->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-sm text-gray-400"
                                colspan="6">
                                {{ __('messages.audit_log.no_entries') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
