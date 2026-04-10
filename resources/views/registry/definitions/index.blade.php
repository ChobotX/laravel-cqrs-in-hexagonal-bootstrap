@extends('layouts.app')

@section('title', __('messages.registry.definitions.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry')],
    ]" />

    <div class="mb-6 flex items-center justify-between">
        @if ($result->total > 0)
            <span
                  class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10">
                {{ $result->total }} {{ trans_choice('messages.registry.definitions.count', $result->total) }}
            </span>
        @else
            <span></span>
        @endif
        <x-primary-button permission="registry.definitions.create"
                          :href="route('registry.definitions.create')"
                          :label="__('messages.registry.definitions.create_action')" />
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.registry.definitions.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.definitions.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.definitions.namespace') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.definitions.slug') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.registry.definitions.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($result->items as $definition)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <span class="text-base font-medium text-gray-900 sm:text-sm">{{ $definition->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <code
                                      class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->namespace }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <code
                                      class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->slug }}</code>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-action-button permission="registry.definitions.read"
                                                     :href="route('registry.definitions.show', [$definition->namespace, $definition->slug])"
                                                     icon="heroicon-o-eye"
                                                     :label="__('messages.registry.definitions.view_action') . ' ' . $definition->name" />
                                    <x-action-button permission="registry.definitions.update"
                                                     :href="route('registry.definitions.edit', [$definition->namespace, $definition->slug])"
                                                     icon="heroicon-o-pencil-square"
                                                     :label="__('messages.registry.definitions.edit_action') . ' ' . $definition->name" />
                                    <x-action-button permission="registry.entries.read"
                                                     :href="route('registry.entries.index', [$definition->namespace, $definition->slug])"
                                                     icon="heroicon-o-rectangle-stack"
                                                     :label="__('messages.registry.definitions.entries_link') . ' ' . $definition->name" />
                                    <x-action-button permission="registry.definitions.delete"
                                                     :action="route('registry.definitions.destroy', [$definition->namespace, $definition->slug])"
                                                     method="DELETE"
                                                     icon="heroicon-o-trash"
                                                     :label="__('messages.registry.definitions.delete_action') . ' ' . $definition->name"
                                                     variant="danger"
                                                     confirm
                                                     :confirm-title="__('messages.registry.definitions.delete_confirm_title')"
                                                     :confirm-message="__('messages.registry.definitions.delete_confirm_message', ['name' => $definition->name])" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table-empty-state colspan="4"
                                             :message="__('messages.registry.definitions.empty')" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :result="$result" />
@endsection
