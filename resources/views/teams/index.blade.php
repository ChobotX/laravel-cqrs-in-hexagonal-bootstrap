@extends('layouts.app')

@section('title', __('messages.teams.title'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span
                  class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10">
                {{ $result->total }} {{ trans_choice('messages.teams.count', $result->total) }}
            </span>
            <div class="flex rounded-lg border border-gray-200 bg-white p-0.5"
                 role="group"
                 aria-label="{{ __('messages.teams.title') }}">
                <button class="rounded-md bg-indigo-100 p-1.5 text-indigo-700 transition-colors"
                        id="teams-view-list-btn"
                        type="button"
                        title="{{ __('messages.teams.view_list') }}"
                        aria-label="{{ __('messages.teams.view_list') }}">
                    <x-heroicon-o-list-bullet class="size-4" />
                </button>
                <button class="rounded-md p-1.5 text-gray-400 transition-colors hover:text-gray-600"
                        id="teams-view-tree-btn"
                        type="button"
                        title="{{ __('messages.teams.view_tree') }}"
                        aria-label="{{ __('messages.teams.view_tree') }}">
                    <x-heroicon-o-rectangle-group class="size-4" />
                </button>
            </div>
        </div>
        <x-primary-button permission="teams.management.create"
                          :href="route('teams.create')"
                          :label="__('messages.teams.create_action')" />
    </div>

    <div id="teams-list-view">
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <caption class="sr-only">{{ __('messages.teams.title') }}</caption>
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">{{ __('messages.teams.name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">{{ __('messages.teams.slug') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">{{ __('messages.teams.parent') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">{{ __('messages.teams.description') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col">{{ __('messages.teams.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($result->items as $team)
                            @php
                                $parentName = null;
                                if ($team->parentTeamId !== null) {
                                    foreach ($result->items as $candidate) {
                                        if ($candidate->id->value === $team->parentTeamId->value) {
                                            $parentName = $candidate->name->value;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <span class="text-base font-medium text-gray-900 sm:text-sm">{{ $team->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <code
                                          class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $team->slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-base text-gray-500 sm:text-sm">{{ $parentName ?? '—' }}</td>
                                <td class="px-6 py-4 text-base text-gray-500 sm:text-sm">{{ $team->description }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-action-button permission="teams.management.read"
                                                         :href="route('teams.show', $team->id)"
                                                         icon="heroicon-o-eye"
                                                         :label="__('messages.teams.view_action') .
                                                             ' ' .
                                                             $team->name" />
                                        <x-action-button permission="teams.management.update"
                                                         :href="route('teams.edit', $team->id)"
                                                         icon="heroicon-o-pencil-square"
                                                         :label="__('messages.teams.edit_action') .
                                                             ' ' .
                                                             $team->name" />
                                        <x-action-button permission="teams.management.delete"
                                                         :action="route('teams.destroy', $team->id)"
                                                         method="DELETE"
                                                         icon="heroicon-o-trash"
                                                         :label="__('messages.teams.delete_action') .
                                                             ' ' .
                                                             $team->name"
                                                         variant="danger"
                                                         confirm
                                                         :confirm-title="__('messages.teams.delete_confirm_title')"
                                                         :confirm-message="__('messages.teams.delete_confirm_message', [
                                                             'name' => $team->name,
                                                         ])" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-pagination :result="$result" />

    <div class="hidden"
         id="teams-tree-view"
         data-team-tree
         data-fetch-url="{{ route('internal-api.teams.tree') }}">
    </div>
@endsection
