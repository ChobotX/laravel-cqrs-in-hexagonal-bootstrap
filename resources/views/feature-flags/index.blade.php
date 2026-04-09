@extends('layouts.app')

@section('title', __('messages.feature_flags.title'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.feature_flags')],
    ]" />

    <div id="app-feature-flags-grid"
         data-fetch-url="{{ route('internal-api.feature-flags.list') }}"
         data-can-update="{{ $canUpdate ? 'true' : 'false' }}"
         data-can-share-team="{{ $canShareTeam ? 'true' : 'false' }}"
         data-can-share-global="{{ $canShareGlobal ? 'true' : 'false' }}"
         data-shareable-teams="{{ json_encode($shareableTeams) }}"
         data-groups="{{ json_encode($groups) }}">
    </div>
@endsection
