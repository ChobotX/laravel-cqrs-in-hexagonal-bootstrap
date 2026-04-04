@extends('layouts.app')

@section('title', __('messages.registry.entries.edit') . ' — ' . $entry->title)

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
        ['label' => $definition->name, 'href' => route('registry.definitions.show', [$definition->namespace, $definition->slug])],
        ['label' => __('messages.registry.entries.title'), 'href' => route('registry.entries.index', [$definition->namespace, $definition->slug])],
        ['label' => $entry->title],
    ]" />

    <div class="max-w-4xl">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.registry.entries.edit_subtitle') }}</p>
                <div class="mt-1 flex items-center gap-2">
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->namespace }}</code>
                    <span class="text-xs text-gray-400">/</span>
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->slug }}</code>
                    <span class="text-xs text-gray-400">v{{ $entry->definitionVersion->value }}</span>
                </div>
            </div>

            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('registry.entries.update', [$definition->namespace, $definition->slug, $entry->id]) }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="title">{{ __('messages.registry.entries.title_field') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="title"
                           name="title"
                           type="text"
                           value="{{ old('title', $entry->title) }}"
                           required
                           @error('title') aria-describedby="title-error" aria-invalid="true" @enderror>
                    @error('title')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="title-error">{{ $message }}</p>
                    @enderror
                </div>

                <div data-schema-form
                     data-schema="{{ $schema }}"
                     data-values="{{ json_encode(old('data', $entry->data)) }}"
                     data-errors="{{ json_encode($errors->get('data.*')) }}"
                     data-field-prefix="data">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.registry.entries.update_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('registry.entries.index', [$definition->namespace, $definition->slug])"
                                      :label="__('messages.registry.entries.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection
