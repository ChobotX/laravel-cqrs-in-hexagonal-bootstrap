@extends('layouts.app')

@section('title', __('messages.registry.definitions.edit'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
        ['label' => $definition->name, 'href' => route('registry.definitions.show', [$definition->namespace, $definition->slug])],
        ['label' => __('messages.registry.definitions.edit')],
    ]" />

    <div>
        {{-- Update name --}}
        <div class="mb-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.registry.definitions.edit_subtitle') }}</p>
                <div class="mt-1 flex items-center gap-2">
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->namespace }}</code>
                    <span class="text-xs text-gray-400">/</span>
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->slug }}</code>
                </div>
            </div>

            <form class="grid grid-cols-1 gap-x-6 gap-y-5 p-6 md:grid-cols-2 xl:grid-cols-4"
                  method="POST"
                  action="{{ route('registry.definitions.update', [$definition->namespace, $definition->slug]) }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.registry.definitions.name') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           type="text"
                           value="{{ old('name', $definition->name) }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.registry.definitions.update_name')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('registry.definitions.show', [$definition->namespace, $definition->slug])"
                                      :label="__('messages.registry.definitions.cancel')" />
                </div>
            </form>
        </div>

        {{-- Versions --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-medium text-gray-900">{{ __('messages.registry.versions.title') }}</h3>
            </div>

            <div class="px-6 py-4">
                @if (count($versions) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <caption class="sr-only">{{ __('messages.registry.versions.title') }}</caption>
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        scope="col">{{ __('messages.registry.versions.version') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        scope="col">{{ __('messages.registry.versions.status') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                        scope="col">{{ __('messages.registry.versions.fields') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                        scope="col">{{ __('messages.registry.versions.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($versions as $version)
                                    <tr class="transition-colors hover:bg-gray-50/50">
                                        <td class="px-4 py-3 text-base font-medium text-gray-900 sm:text-sm">
                                            v{{ $version['version'] }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                  class="{{ $version['statusClasses'] }} inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1">
                                                {{ $version['statusLabel'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-base text-gray-500 sm:text-sm">
                                            {{ $version['fieldCount'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                @if ($version['activateRoute'])
                                                    <x-action-button skip-permission
                                                                     :action="$version['activateRoute']"
                                                                     icon="heroicon-o-check-circle"
                                                                     :label="__('messages.registry.versions.activate_action')" />
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('messages.registry.versions.no_versions') }}</p>
                @endif
            </div>

            {{-- Schema Builder --}}
            <div class="border-t border-gray-200 px-6 py-4">
                <h4 class="mb-3 text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.registry.versions.schema_builder') }}</h4>
                <div data-schema-builder
                     data-action="{{ route('registry.versions.store', [$definition->namespace, $definition->slug]) }}"
                     data-csrf="{{ csrf_token() }}">
                </div>
            </div>
        </div>
    </div>
@endsection
