@extends('layouts.app')

@section('title', __('messages.roles.create'))

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.roles.create_subtitle') }}</p>
            </div>

            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('roles.store') }}">
                @csrf

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.roles.name') }}</label>
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
                           for="description">{{ __('messages.roles.description') }}</label>
                    <textarea class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                              id="description"
                              name="description"
                              rows="3"
                              @error('description') aria-describedby="description-error" aria-invalid="true" @enderror>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="description-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <h3 class="mb-3 text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.roles.permissions') }}
                    </h3>
                    @include('components.permission-matrix', ['modules' => $modules, 'permissions' => []])
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.roles.create_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('roles.index')"
                                      :label="__('messages.roles.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection
