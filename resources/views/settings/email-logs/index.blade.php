@extends('layouts.app')

@section('title', __('messages.email_logs.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.settings'), 'href' => route('settings.index')],
        ['label' => __('messages.email_logs.title')],
    ]" />

    @if ($logs->total > 0)
        <div class="mb-6 flex items-center justify-between">
            <span
                  class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                {{ trans_choice('messages.email_logs.count', $logs->total) }}
            </span>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200"
                   data-testid="email-logs-table">
                <caption class="sr-only">{{ __('messages.email_logs.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="w-8 px-6 py-3"
                            scope="col">
                            <span class="sr-only">{{ __('messages.email_logs.expand') }}</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_logs.recipient') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_logs.template_type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_logs.subject') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_logs.sent_at') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs->items as $log)
                        <tr class="group transition-colors hover:bg-gray-50/50"
                            data-testid="log-row-{{ $log->id->value }}">
                            <td class="px-6 py-4">
                                <button class="cursor-pointer text-gray-400 transition-colors hover:text-indigo-600"
                                        type="button"
                                        aria-expanded="false"
                                        title="{{ __('messages.email_logs.toggle_details') }}"
                                        data-email-log-toggle
                                        data-testid="toggle-log-{{ $log->id->value }}">
                                    <svg class="h-5 w-5 transition-transform"
                                         aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke-width="1.5"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $log->recipientEmail }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-300/50">
                                    {{ $log->templateType }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ $log->renderedSubject }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500">{{ $log->sentAt->format('Y-m-d H:i:s') }}</span>
                            </td>
                        </tr>
                        <tr class="hidden"
                            data-testid="log-detail-{{ $log->id->value }}">
                            <td class="bg-gray-50/30 px-6 py-4"
                                colspan="5">
                                <div class="ml-8 space-y-3">
                                    <div>
                                        <span class="mb-1 block text-xs font-medium text-gray-500">
                                            {{ __('messages.email_logs.body_preview') }}
                                        </span>
                                        <div class="max-h-40 overflow-y-auto rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700">
                                            {!! nl2br(e(Str::limit($log->renderedBodyMasked, 500))) !!}
                                        </div>
                                    </div>
                                    @if (count($log->variableKeys) > 0)
                                        <div>
                                            <span class="mb-1 block text-xs font-medium text-gray-500">
                                                {{ __('messages.email_logs.variables_used') }}
                                            </span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($log->variableKeys as $key)
                                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                                        {{ $key }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table-empty-state colspan="5"
                                             :message="__('messages.email_logs.empty')" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :result="$logs" />
@endsection
