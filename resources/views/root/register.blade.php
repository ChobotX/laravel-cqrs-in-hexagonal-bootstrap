@extends('layouts.guest')

@section('title', 'Register workspace')

@section('content')
    <h2 class="text-center text-xl font-semibold text-gray-900">Create your workspace</h2>
    <p class="mb-6 mt-1 text-center text-base text-gray-500 sm:text-sm">Set up a new tenant workspace</p>

    @include('components.flash')

    <form class="space-y-5"
          method="POST"
          action="{{ route('root.register.store') }}">
        @csrf

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="name">Workspace name</label>
            <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                   id="name"
                   name="name"
                   type="text"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   placeholder="Acme Corp"
                   @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
            @error('name')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="name-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="slug">Slug</label>
            <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                   id="slug"
                   name="slug"
                   type="text"
                   value="{{ old('slug') }}"
                   required
                   placeholder="acme"
                   pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?"
                   @error('slug') aria-describedby="slug-error" aria-invalid="true" @enderror>
            @error('slug')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="slug-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="domain">Subdomain</label>
            <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                   id="domain"
                   name="domain"
                   type="text"
                   value="{{ old('domain') }}"
                   required
                   placeholder="acme"
                   pattern="[a-z0-9]([a-z0-9-]*[a-z0-9])?"
                   @error('domain') aria-describedby="domain-error" aria-invalid="true" @enderror>
            @error('domain')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="domain-error">{{ $message }}</p>
            @enderror
        </div>

        <x-primary-button skip-permission
                          variant="login"
                          :label="'Create workspace'" />
    </form>
@endsection
