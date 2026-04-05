@extends('layouts.app')

@section('title', __('messages.roles.edit'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.roles'), 'href' => route('roles.index')],
        ['label' => __('messages.roles.edit')],
    ]" />

    <div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.roles.edit_subtitle') }}</p>
            </div>

            <form class="grid grid-cols-1 gap-x-6 gap-y-5 p-6 md:grid-cols-2 xl:grid-cols-4"
                  method="POST"
                  action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.roles.name') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="name"
                           name="name"
                           type="text"
                           value="{{ old('name', $role->name) }}"
                           required
                           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
                    @error('name')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="name-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="description">{{ __('messages.roles.description') }}</label>
                    <textarea class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                              id="description"
                              name="description"
                              rows="3"
                              @error('description') aria-describedby="description-error" aria-invalid="true" @enderror>{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="description-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full">
                    <h3 class="mb-3 text-base font-medium text-gray-700 sm:text-sm">{{ __('messages.roles.permissions') }}
                    </h3>
                    @include('components.permission-matrix', [
                        'modules' => $modules,
                        'permissions' => $role->permissions,
                    ])
                </div>

                <div class="col-span-full flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.roles.update_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('roles.index')"
                                      :label="__('messages.roles.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection
