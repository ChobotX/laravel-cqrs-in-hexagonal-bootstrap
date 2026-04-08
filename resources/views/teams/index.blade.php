@extends('layouts.app')

@section('title', __('messages.teams.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.teams')],
    ]" />

    <div id="app-teams-grid"
         data-fetch-url="{{ route('internal-api.teams.list') }}"
         data-create-url="{{ route('teams.create') }}"
         data-can-create="{{ $canCreate ? 'true' : 'false' }}"
         data-can-read="{{ $canRead ? 'true' : 'false' }}"
         data-can-update="{{ $canUpdate ? 'true' : 'false' }}"
         data-can-delete="{{ $canDelete ? 'true' : 'false' }}"
         data-tree-view-url="{{ route('teams.index') }}#tree"
         data-can-share-team="{{ $canShareTeam ? 'true' : 'false' }}"
         data-can-share-global="{{ $canShareGlobal ? 'true' : 'false' }}"
         data-shareable-teams="{{ json_encode($shareableTeams) }}">
    </div>
@endsection
