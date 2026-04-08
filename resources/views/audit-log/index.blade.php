@extends('layouts.app')

@section('title', __('messages.audit_log.title'))

@section('content')
    <div class="mb-6">
        <div class="overflow-hidden rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <form method="GET"
                  action="{{ route('audit-log.index') }}"
                  class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500"
                           for="filter-entity-type">{{ __('messages.audit_log.filter_entity_type') }}</label>
                    <input class="mt-1 block w-32 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           id="filter-entity-type"
                           name="entity_type"
                           type="text"
                           value="{{ $filters['entity_type'] ?? '' }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500"
                           for="filter-user">{{ __('messages.audit_log.filter_user') }}</label>
                    <input class="mt-1 block w-64 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           id="filter-user"
                           name="user_id"
                           type="text"
                           value="{{ $filters['user_id'] ?? '' }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500"
                           for="filter-trace">{{ __('messages.audit_log.filter_trace') }}</label>
                    <input class="mt-1 block w-64 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           id="filter-trace"
                           name="trace_id"
                           type="text"
                           value="{{ $filters['trace_id'] ?? '' }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500"
                           for="filter-from">{{ __('messages.audit_log.filter_from') }}</label>
                    <input class="mt-1 block w-40 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           id="filter-from"
                           name="from"
                           type="date"
                           value="{{ $filters['from'] ?? '' }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500"
                           for="filter-to">{{ __('messages.audit_log.filter_to') }}</label>
                    <input class="mt-1 block w-40 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           id="filter-to"
                           name="to"
                           type="date"
                           value="{{ $filters['to'] ?? '' }}">
                </div>
                <button
                        class="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500"
                        type="submit">{{ __('messages.audit_log.filter') }}</button>
                <a class="cursor-pointer rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-200"
                   href="{{ route('audit-log.index') }}">{{ __('messages.audit_log.clear') }}</a>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.audit_log.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.occurred_at') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.action') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.entity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.user') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.audit_log.trace_id') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($entries as $entry)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $entry->occurredAt->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $entry->actionLabel }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($entry->entityType)
                                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-500/10">
                                        {{ $entry->entityType }}
                                    </span>
                                    <span class="ml-1 font-mono text-xs text-gray-400">{{ Str::limit($entry->entityId ?? '', 8, '...') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($entry->userId)
                                    <span class="font-mono text-xs">{{ Str::limit($entry->userId, 8, '...') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if ($entry->status->value === 'success')
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-600/10">
                                        {{ __('messages.audit_log.success') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-red-600/10">
                                        {{ __('messages.audit_log.failure') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a class="cursor-pointer font-mono text-xs text-indigo-600 hover:text-indigo-800"
                                   href="{{ route('audit-log.trace', $entry->traceId) }}">{{ Str::limit($entry->traceId, 12, '...') }}</a>
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
