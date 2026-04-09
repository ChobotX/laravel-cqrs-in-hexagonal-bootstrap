@extends('layouts.app')

@section('title', __('messages.email_templates.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.settings'), 'href' => route('settings.index')],
        ['label' => __('messages.email_templates.title')],
    ]" />

    <div class="mb-6 flex items-center justify-between">
        <span
              class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
            {{ count($templates) }} {{ trans_choice('messages.email_templates.count', count($templates)) }}
        </span>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200"
                   data-testid="email-templates-table">
                <caption class="sr-only">{{ __('messages.email_templates.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_templates.name') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_templates.description') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_templates.last_modified') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.email_templates.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($templates as $template)
                        <tr class="transition-colors hover:bg-gray-50/50"
                            data-testid="template-row-{{ $template['type'] }}">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ __('messages.email_templates.types.' . $template['type'] . '.name') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-500">{{ __('messages.email_templates.types.' . $template['type'] . '.description') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if ($template['updatedAt'])
                                    <span class="text-sm text-gray-500">{{ $template['updatedAt'] }}</span>
                                @else
                                    <span class="text-sm text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-action-button skip-permission
                                                 :href="route('settings.email-templates.edit', [
                                                     'type' => $template['type'],
                                                     'locale' => app()->getLocale(),
                                                 ])"
                                                 icon="heroicon-o-pencil-square"
                                                 :label="__('messages.email_templates.edit_action')"
                                                 data-testid="edit-template-{{ $template['type'] }}" />
                            </td>
                        </tr>
                    @empty
                        <x-table-empty-state colspan="4"
                                             :message="__('messages.email_templates.empty')" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
