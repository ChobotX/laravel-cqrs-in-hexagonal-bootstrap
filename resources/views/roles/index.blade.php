@extends('layouts.app')

@section('title', __('messages.roles.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.roles')],
    ]" />

    <div id="app-roles-grid"
         data-fetch-url="{{ route('internal-api.roles.list') }}"
         data-create-url="{{ route('roles.create') }}"
         data-can-update="{{ $canUpdate ? 'true' : 'false' }}"
         data-can-share-team="{{ $canShareTeam ? 'true' : 'false' }}"
         data-can-share-global="{{ $canShareGlobal ? 'true' : 'false' }}"
         data-shareable-teams="{{ json_encode($shareableTeams) }}">
    </div>
@endsection
