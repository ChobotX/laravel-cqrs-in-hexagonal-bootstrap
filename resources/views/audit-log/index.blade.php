@extends('layouts.app')

@section('title', __('messages.audit_log.title'))

@section('content')
    <div class="mb-6">
        <x-form-card>
            <form class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6"
                  method="GET"
                  action="{{ route('audit-log.index') }}">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                           for="filter-entity-type">{{ __('messages.audit_log.filter_entity_type') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="filter-entity-type"
                           name="entity_type"
                           type="text"
                           value="{{ $filters['entity_type'] ?? '' }}"
                           placeholder="user, role...">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                           for="filter-user">{{ __('messages.audit_log.filter_user') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="filter-user"
                           name="user_id"
                           type="text"
                           value="{{ $filters['user_id'] ?? '' }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                           for="filter-trace">{{ __('messages.audit_log.filter_trace') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="filter-trace"
                           name="trace_id"
                           type="text"
                           value="{{ $filters['trace_id'] ?? '' }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                           for="filter-from">{{ __('messages.audit_log.filter_from') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="filter-from"
                           name="from"
                           type="date"
                           value="{{ $filters['from'] ?? '' }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700"
                           for="filter-to">{{ __('messages.audit_log.filter_to') }}</label>
                    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="filter-to"
                           name="to"
                           type="date"
                           value="{{ $filters['to'] ?? '' }}">
                </div>
                <div class="flex items-end gap-2">
                    <button class="cursor-pointer rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            type="submit"
                            title="{{ __('messages.audit_log.filter') }}">{{ __('messages.audit_log.filter') }}</button>
                    <a class="cursor-pointer rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                       href="{{ route('audit-log.index') }}"
                       title="{{ __('messages.audit_log.clear') }}">{{ __('messages.audit_log.clear') }}</a>
                </div>
            </form>
        </x-form-card>
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
                                    <span
                                          class="inline-flex items-center rounded-full bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-gray-500/10">
                                        {{ $entry->entityType }}
                                    </span>
                                    <span
                                          class="ml-1 font-mono text-xs text-gray-400">{{ Str::limit($entry->entityId ?? '', 8, '...') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($entry->userId)
                                    <span class="font-mono text-xs">{{ Str::limit($entry->userId, 8, '...') }}</span>
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
                            <td class="px-6 py-4 text-sm">
                                <a class="cursor-pointer font-mono text-xs text-indigo-600 hover:text-indigo-800"
                                   href="{{ route('audit-log.trace', $entry->traceId) }}"
                                   title="{{ $entry->traceId }}">{{ Str::limit($entry->traceId, 12, '...') }}</a>
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
