@extends('layouts.app')

@section('title', __('messages.registry.entries.title') . ' — ' . $definition->name)

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
        ['label' => $definition->name, 'href' => route('registry.definitions.show', [$definition->namespace, $definition->slug])],
        ['label' => __('messages.registry.entries.title')],
    ]" />

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span
                  class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10">
                {{ $result->total }} {{ trans_choice('messages.registry.entries.count', $result->total) }}
            </span>
            <span class="text-base text-gray-500 sm:text-sm">
                <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->namespace }}</code>
                <span class="text-xs text-gray-400">/</span>
                <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->slug }}</code>
            </span>
        </div>
        <x-primary-button permission="registry.entries.create"
                          :href="route('registry.entries.create', [$definition->namespace, $definition->slug])"
                          :label="__('messages.registry.entries.create_action')" />
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.registry.entries.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.entries.title_field') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.entries.version_field') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.entries.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($result->items as $entry)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <span class="text-base font-medium text-gray-900 sm:text-sm">{{ $entry->title }}</span>
                            </td>
                            <td class="px-6 py-4 text-base text-gray-500 sm:text-sm">
                                v{{ $entry->definitionVersion->value }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-action-button permission="registry.entries.update"
                                                     :href="route('registry.entries.edit', [$definition->namespace, $definition->slug, $entry->id])"
                                                     icon="heroicon-o-pencil-square"
                                                     :label="__('messages.registry.entries.edit') . ' ' . $entry->title" />
                                    <x-action-button permission="registry.entries.delete"
                                                     :action="route('registry.entries.destroy', [$definition->namespace, $definition->slug, $entry->id])"
                                                     method="DELETE"
                                                     icon="heroicon-o-trash"
                                                     :label="__('messages.registry.entries.delete_action') . ' ' . $entry->title"
                                                     variant="danger"
                                                     confirm
                                                     :confirm-title="__('messages.registry.entries.delete_confirm_title')"
                                                     :confirm-message="__('messages.registry.entries.delete_confirm_message', ['name' => $entry->title])" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table-empty-state colspan="3"
                                             :message="__('messages.registry.entries.empty')" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :result="$result" />
@endsection
