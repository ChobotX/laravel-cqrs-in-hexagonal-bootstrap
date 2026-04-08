@extends('layouts.app')

@section('title', __('messages.registry.entries.title') . ' — ' . $definition->name)

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.registry'), 'href' => route('registry.definitions.index')],
        ['label' => $definition->name, 'href' => route('registry.definitions.show', [$definition->namespace, $definition->slug])],
        ['label' => __('messages.registry.entries.title')],
    ]" />

    <div id="app-entries-grid"
         data-fetch-url="{{ route('internal-api.registry.entries.list', [$namespace, $slug]) }}"
         data-create-url="{{ route('registry.entries.create', [$namespace, $slug]) }}"
         data-definition-name="{{ $definition->slug }}"
         data-can-share-team="{{ $canShareTeam ? 'true' : 'false' }}"
         data-can-share-global="{{ $canShareGlobal ? 'true' : 'false' }}"
         data-shareable-teams="{{ json_encode($shareableTeams) }}">
    </div>
@endsection
