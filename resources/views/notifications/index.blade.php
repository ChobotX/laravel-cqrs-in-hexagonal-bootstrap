@extends('layouts.app')

@section('title', __('messages.notifications.title'))

@section('content')
    <div class="mb-6">
        <p class="text-base text-gray-500 sm:text-sm">{{ __('messages.notifications.subtitle') }}</p>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
        <div id="app-notification-list"
             data-user-id="{{ auth()->user()->id }}">
        </div>
    </div>
@endsection
