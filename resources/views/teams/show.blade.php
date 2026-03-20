@extends('layouts.app')

@section('title', $team->name)

@section('content')
    <div class="max-w-4xl">
        <div class="mb-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900 sm:text-sm">{{ $team->name }}</h2>
                <code class="mt-1 rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $team->slug }}</code>
                @if ($team->description)
                    <p class="mt-1 text-base text-gray-500 sm:text-sm">{{ $team->description }}</p>
                @endif
            </div>

            <div class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('teams.index', $organization->id)"
                                      :label="__('messages.teams.back')" />
                </div>
            </div>
        </div>

        {{-- Members --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-medium text-gray-900">{{ __('messages.teams.members') }}</h3>
                @hasPermission('teams.members.update')
                    <form class="flex items-center gap-2"
                          id="add-team-member-form"
                          method="POST"
                          action="{{ route('teams.members', [$organization->id, $team->id]) }}">
                        @csrf
                        <input name="_action"
                               type="hidden"
                               value="add_member">
                        <div data-autocomplete
                             data-search-url="{{ route('internal-api.users.search') }}"
                             data-exclude-ids="{{ json_encode(collect($members)->pluck('userId')->all()) }}"
                             data-input-name="user_id"
                             data-placeholder="{{ __('messages.teams.search_member') }}"
                             data-no-results-text="{{ __('messages.teams.no_results') }}">
                        </div>
                        <x-primary-button skip-permission
                                          :label="__('messages.teams.add_member')" />
                    </form>
                @endhasPermission
            </div>
            <div class="px-6 py-4">
                @if (count($members) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($members as $member)
                            <div
                                 class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 ring-1 ring-indigo-700/10">
                                <span>{{ $member->userName }}</span>
                                @hasPermission('teams.members.update')
                                    <form class="inline"
                                          method="POST"
                                          action="{{ route('teams.members', [$organization->id, $team->id]) }}">
                                        @csrf
                                        <input name="_action"
                                               type="hidden"
                                               value="remove_member">
                                        <input name="user_id"
                                               type="hidden"
                                               value="{{ $member->userId }}">
                                        <x-icon-button class="text-indigo-400 transition-colors hover:text-indigo-700"
                                                       skip-permission
                                                       icon="heroicon-o-x-mark"
                                                       :label="__('messages.teams.remove_member') .
                                                           ' ' .
                                                           $member->userName" />
                                    </form>
                                @endhasPermission
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">{{ __('messages.teams.no_members') }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
