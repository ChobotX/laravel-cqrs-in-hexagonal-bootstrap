@extends('layouts.app')

@section('title', $definition->name)

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
        ['label' => $definition->name],
    ]" />

    <div class="max-w-4xl">
        {{-- Definition header --}}
        <div class="mb-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900 sm:text-sm">{{ $definition->name }}</h2>
                <div class="mt-1 flex items-center gap-2">
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->namespace }}</code>
                    <span class="text-xs text-gray-400">/</span>
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $definition->slug }}</code>
                </div>
            </div>

            <div class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <x-primary-button permission="registry.definitions.update"
                                      :href="route('registry.definitions.edit', [$definition->namespace, $definition->slug])"
                                      :label="__('messages.registry.definitions.edit')" />
                    <x-primary-button permission="registry.entries.read"
                                      :href="route('registry.entries.index', [$definition->namespace, $definition->slug])"
                                      :label="__('messages.registry.definitions.entries_link')" />
                </div>
            </div>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('messages.registry.versions.no_versions') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
