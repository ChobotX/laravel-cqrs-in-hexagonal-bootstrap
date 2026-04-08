@extends('layouts.app')

@section('title', __('messages.users.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.users')],
    ]" />

    <div id="app-users-grid"
         data-fetch-url="{{ route('internal-api.users.list') }}"
         data-create-url="{{ route('users.create') }}"
         data-can-create="{{ $canCreate ? 'true' : 'false' }}"
         data-can-share-team="{{ $canShareTeam ? 'true' : 'false' }}"
         data-can-share-global="{{ $canShareGlobal ? 'true' : 'false' }}"
         data-shareable-teams="{{ json_encode($shareableTeams) }}">
    </div>
@endsection
