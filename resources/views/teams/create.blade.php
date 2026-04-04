@extends('layouts.app')

@section('title', __('messages.teams.create'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.teams'), 'href' => route('teams.index')],
        ['label' => __('messages.teams.create')],
    ]" />

    <div class="max-w-4xl">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.teams.create_subtitle') }}</p>
            </div>

            <form class="space-y-5 p-6"
                  method="POST"
                  action="{{ route('teams.store') }}">
                @csrf

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="name">{{ __('messages.teams.name') }}</label>
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
                           for="slug">{{ __('messages.teams.slug') }}</label>
                    <input class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                           id="slug"
                           name="slug"
                           type="text"
                           value="{{ old('slug') }}"
                           required
                           pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?"
                           minlength="2"
                           maxlength="63"
                           @error('slug') aria-describedby="slug-error" aria-invalid="true" @enderror>
                    <p class="mt-1 text-xs text-gray-400">{{ __('messages.teams.slug_hint') }}</p>
                    @error('slug')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="slug-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="parent_team_id">{{ __('messages.teams.parent') }}</label>
                    <select class="block w-full max-w-md rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                            id="parent_team_id"
                            name="parent_team_id">
                        <option value="">{{ __('messages.teams.no_parent') }}</option>
                        @foreach ($teams as $parentTeam)
                            <option value="{{ $parentTeam->id }}"
                                    @selected(old('parent_team_id') === $parentTeam->id->value)>{{ $parentTeam->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_team_id')
                        <p class="mt-1 text-base text-red-600 sm:text-sm"
                           id="parent_team_id-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                           for="description">{{ __('messages.teams.description') }}</label>
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

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button skip-permission
                                      :label="__('messages.teams.create_action')" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('teams.index')"
                                      :label="__('messages.teams.cancel')" />
                </div>
            </form>
        </div>
    </div>
@endsection
