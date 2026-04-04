@extends('layouts.app')

@section('title', __('messages.registry.definitions.create'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
        ['label' => __('messages.registry.definitions.create')],
    ]" />

    <div class="max-w-4xl">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.registry.definitions.create_subtitle') }}</p>
            </div>

            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('registry.definitions.store') }}">
                @csrf

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.registry.definitions.name') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           type="text"
                           value="{{ old('name') }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="namespace">{{ __('messages.registry.definitions.namespace') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="namespace"
                           name="namespace"
                           type="text"
                           value="{{ old('namespace') }}"
                           required
                           pattern="[a-z][a-z0-9_]*"
                           maxlength="63"
                           @error('namespace') aria-describedby="namespace-error" aria-invalid="true" @enderror>
                    <p class="mt-1 text-xs text-gray-400">{{ __('messages.registry.definitions.slug_hint') }}</p>
                    @error('namespace')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="namespace-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="slug">{{ __('messages.registry.definitions.slug') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="slug"
                           name="slug"
                           type="text"
                           value="{{ old('slug') }}"
                           required
                           pattern="[a-z][a-z0-9_]*"
                           maxlength="63"
                           @error('slug') aria-describedby="slug-error" aria-invalid="true" @enderror>
                    <p class="mt-1 text-xs text-gray-400">{{ __('messages.registry.definitions.slug_hint') }}</p>
                    @error('slug')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="slug-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.registry.definitions.create_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('registry.definitions.index')"
                                      :label="__('messages.registry.definitions.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection
