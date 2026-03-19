@extends('layouts.app')

@section('title', __('messages.organizations.title'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <span
              class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10">
            {{ count($organizations) }} {{ trans_choice('messages.organizations.count', count($organizations)) }}
        </span>
        <x-primary-button permission="organizations.management.create"
                          :href="route('organizations.create')"
                          :label="__('messages.organizations.create_action')" />
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.organizations.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.organizations.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.organizations.slug') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.organizations.description') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.organizations.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($organizations as $organization)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <span
                                      class="text-base font-medium text-gray-900 sm:text-sm">{{ $organization->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <code
                                      class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $organization->slug }}</code>
                            </td>
                            <td class="px-6 py-4 text-base text-gray-500 sm:text-sm">{{ $organization->description }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-action-button permission="organizations.management.read"
                                                     :href="route('organizations.show', $organization->id)"
                                                     icon="heroicon-o-eye"
                                                     :label="__('messages.organizations.view_action') .
                                                         ' ' .
                                                         $organization->name" />
                                    <x-action-button permission="organizations.management.update"
                                                     :href="route('organizations.edit', $organization->id)"
                                                     icon="heroicon-o-pencil-square"
                                                     :label="__('messages.organizations.edit_action') .
                                                         ' ' .
                                                         $organization->name" />
                                    <x-action-button permission="organizations.management.delete"
                                                     :action="route('organizations.destroy', $organization->id)"
                                                     method="DELETE"
                                                     icon="heroicon-o-trash"
                                                     :label="__('messages.organizations.delete_action') .
                                                         ' ' .
                                                         $organization->name"
                                                     variant="danger"
                                                     confirm
                                                     :confirm-title="__('messages.organizations.delete_confirm_title')"
                                                     :confirm-message="__('messages.organizations.delete_confirm_message', [
                                                         'name' => $organization->name,
                                                     ])" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
