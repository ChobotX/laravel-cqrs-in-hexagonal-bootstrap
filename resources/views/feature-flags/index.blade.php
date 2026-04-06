@extends('layouts.app')

@section('title', __('messages.feature_flags.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.feature_flags')],
    ]" />

    <div class="mb-6 flex items-center justify-between">
        <span
              class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
            {{ trans_choice('messages.feature_flags.count', $flagCount) }}
        </span>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <caption class="sr-only">{{ __('messages.feature_flags.title') }}</caption>
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.feature_flags.flag') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.feature_flags.type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.feature_flags.status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.feature_flags.value') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                            scope="col">
                            {{ __('messages.feature_flags.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($groups as $groupKey => $group)
                        <tr class="bg-gray-50/30">
                            <td class="px-6 py-2"
                                colspan="5">
                                <span class="text-sm font-semibold text-gray-700">{{ __($group['label']) }}</span>
                            </td>
                        </tr>
                        @foreach ($group['flags'] as $flag)
                            <tr class="transition-colors hover:bg-gray-50/50"
                                data-testid="flag-row-{{ $flag->definition->key->value }}">
                                <td class="px-6 py-4 pl-10">
                                    <div>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                  class="text-sm font-medium text-gray-900">{{ __($flag->definition->label) }}</span>
                                            <code
                                                  class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $flag->definition->key->value }}</code>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-400">{{ __($flag->definition->description) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($flag->definition->type === $flagTypeBoolean)
                                        <span
                                              class="inline-flex items-center rounded-full bg-purple-50 px-2 py-0.5 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                            {{ __('messages.feature_flags.type_boolean') }}
                                        </span>
                                    @elseif ($flag->definition->type === $flagTypeSelect)
                                        <span
                                              class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            {{ __('messages.feature_flags.type_select') }}
                                        </span>
                                    @else
                                        <span
                                              class="inline-flex items-center rounded-full bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-700 ring-1 ring-inset ring-teal-700/10">
                                            {{ __('messages.feature_flags.type_input') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST"
                                          action="{{ route('feature-flags.update', $flag->definition->key->value) }}">
                                        @csrf
                                        @method('PUT')
                                        <x-toggle-switch name="enabled"
                                                         :checked="$flag->isEnabled()"
                                                         :auto-submit="true" />
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if ($flag->definition->type !== $flagTypeBoolean)
                                            <span
                                                  class="{{ $flag->isOverridden ? 'font-medium text-gray-900' : 'text-gray-400' }}">
                                                {{ $flag->value }}
                                            </span>
                                        @endif
                                        @if ($flag->isOverridden)
                                            <span
                                                  class="inline-flex items-center rounded-full bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                                                {{ __('messages.feature_flags.override_badge') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($flag->definition->type !== $flagTypeBoolean)
                                            <x-action-button skip-permission
                                                             :href="route(
                                                                 'feature-flags.edit',
                                                                 $flag->definition->key->value,
                                                             )"
                                                             icon="heroicon-o-pencil-square"
                                                             :label="__('messages.feature_flags.edit_action') .
                                                                 ' ' .
                                                                 $flag->definition->key->value" />
                                        @endif
                                        @if ($flag->isOverridden)
                                            <x-action-button skip-permission
                                                             :action="route(
                                                                 'feature-flags.reset',
                                                                 $flag->definition->key->value,
                                                             )"
                                                             method="DELETE"
                                                             icon="heroicon-o-arrow-uturn-left"
                                                             :label="__('messages.feature_flags.reset_action') .
                                                                 ' ' .
                                                                 $flag->definition->key->value"
                                                             confirm
                                                             :confirm-title="__(
                                                                 'messages.feature_flags.reset_confirm_title',
                                                             )"
                                                             :confirm-message="__(
                                                                 'messages.feature_flags.reset_confirm_message',
                                                                 ['key' => $flag->definition->key->value],
                                                             )" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <x-table-empty-state colspan="5"
                                             :message="__('messages.feature_flags.empty')" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
