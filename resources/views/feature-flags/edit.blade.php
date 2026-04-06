@extends('layouts.app')

@section('title', __('messages.feature_flags.edit'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.feature_flags'), 'href' => route('feature-flags.index')],
        ['label' => $flag->definition->key->value],
    ]" />

    <div class="max-w-2xl">
        <x-form-card :title="__($flag->definition->label)"
                     :subtitle="__($flag->definition->description)">
            <form method="POST"
                  action="{{ route('feature-flags.update', $flag->definition->key->value) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="mb-1 block text-xs font-medium text-gray-500">
                                {{ __('messages.feature_flags.flag') }}
                            </span>
                            <code
                                  class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $flag->definition->key->value }}</code>
                        </div>
                        <div>
                            <span class="mb-1 block text-xs font-medium text-gray-500">
                                {{ __('messages.feature_flags.type') }}
                            </span>
                            @if ($flag->definition->type === $flagTypeSelect)
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
                        </div>
                    </div>

                    <div>
                        <span class="mb-1 block text-xs font-medium text-gray-500">
                            {{ __('messages.feature_flags.default_value') }}
                        </span>
                        <span class="text-sm text-gray-600">{{ $flag->definition->default }}</span>
                    </div>

                    <div class="border-t border-gray-200 pt-5">
                        <div class="mb-4 flex items-center gap-3">
                            <x-toggle-switch name="enabled"
                                             :checked="$flag->isEnabled()" />
                            <label class="text-base font-medium text-gray-700 sm:text-sm"
                                   for="enabled">
                                {{ __('messages.feature_flags.enabled') }}
                            </label>
                        </div>

                        <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                               for="value">
                            {{ __('messages.feature_flags.value') }}
                        </label>

                        @if ($flag->definition->type === $flagTypeSelect)
                            <select class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                                    id="value"
                                    name="value">
                                @foreach ($flag->definition->options as $option)
                                    <option value="{{ $option }}"
                                            {{ $flag->value === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                                   id="value"
                                   name="value"
                                   type="text"
                                   value="{{ old('value', $flag->value) }}">
                            @if ($flag->definition->pattern !== null)
                                <p class="mt-1 text-xs text-gray-400">
                                    {{ __('messages.feature_flags.pattern_hint', ['pattern' => $flag->definition->pattern]) }}
                                </p>
                            @endif
                            @error('value')
                                <p class="mt-1 text-base text-red-600 sm:text-sm"
                                   id="value-error">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    @if ($flag->isOverridden)
                        <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3">
                            <p class="text-xs text-indigo-700">
                                {{ __('messages.feature_flags.override_active', ['default' => $flag->definition->default]) }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button skip-permission
                                      :label="__('messages.feature_flags.save')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('feature-flags.index')"
                                      :label="__('messages.feature_flags.cancel')" />
                </div>
            </form>
        </x-form-card>
    </div>
@endsection
