@extends('layouts.app')

@section('title', __('messages.roles.title'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <span
              class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10">
            {{ $result->total }} {{ trans_choice('messages.roles.count', $result->total) }}
        </span>
        <x-primary-button permission="users.roles.update"
                          :href="route('roles.create')"
                          :label="__('messages.roles.create_action')" />
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.roles.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <x-sortable-header column="name"
                                           :label="__('messages.roles.name')"
                                           :sorting="$sorting" />
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.description') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">{{ __('messages.roles.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($result->items as $role)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-base font-medium text-gray-900 sm:text-sm">{{ $role->name }}</span>
                                    @if ($role->isSystem)
                                        <span
                                              class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-amber-700/10">
                                            {{ __('messages.roles.system_badge') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-base text-gray-500 sm:text-sm">{{ $role->description }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-action-button permission="users.roles.read"
                                                     :href="route('roles.show', $role->id)"
                                                     icon="heroicon-o-eye"
                                                     :label="__('messages.roles.view_action') . ' ' . $role->name" />
                                    <x-action-button permission="users.roles.update"
                                                     :href="route('roles.edit', $role->id)"
                                                     icon="heroicon-o-pencil-square"
                                                     :label="__('messages.roles.edit_action') . ' ' . $role->name" />
                                    @unless ($role->isSystem)
                                        <x-action-button permission="users.roles.update"
                                                         :action="route('roles.destroy', $role->id)"
                                                         method="DELETE"
                                                         icon="heroicon-o-trash"
                                                         :label="__('messages.roles.delete_action') .
                                                             ' ' .
                                                             $role->name"
                                                         variant="danger"
                                                         confirm
                                                         :confirm-title="__('messages.roles.delete_confirm_title')"
                                                         :confirm-message="__('messages.roles.delete_confirm_message', [
                                                             'name' => $role->name,
                                                         ])" />
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :result="$result" />
@endsection
