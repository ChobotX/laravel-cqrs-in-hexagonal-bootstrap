@extends('layouts.guest')

@section('title', 'Welcome')

@section('content')
    <h2 class="text-center text-xl font-semibold text-gray-900">Welcome to Bootstrap</h2>
    <p class="mb-6 mt-1 text-center text-base text-gray-500 sm:text-sm">Multi-tenant SaaS platform</p>

    <div class="space-y-3">
        <a class="flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-base font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 sm:text-sm"
           href="{{ route('root.register') }}"
           title="Create a new workspace">
            Create a new workspace
        </a>
    </div>
@endsection
