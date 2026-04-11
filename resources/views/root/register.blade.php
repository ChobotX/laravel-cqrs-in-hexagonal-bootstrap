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
                   data-testid="register-name-input"
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
                   data-testid="register-slug-input"
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
                   data-testid="register-domain-input"
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

        <hr class="border-gray-200">

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="admin_name">Admin name</label>
            <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                   id="admin_name"
                   name="admin_name"
                   data-testid="register-admin-name-input"
                   type="text"
                   value="{{ old('admin_name') }}"
                   required
                   placeholder="John Doe"
                   @error('admin_name') aria-describedby="admin_name-error" aria-invalid="true" @enderror>
            @error('admin_name')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="admin_name-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
                   for="admin_email">Admin email</label>
            <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
                   id="admin_email"
                   name="admin_email"
                   data-testid="register-admin-email-input"
                   type="email"
                   value="{{ old('admin_email') }}"
                   required
                   placeholder="admin@acme.com"
                   @error('admin_email') aria-describedby="admin_email-error" aria-invalid="true" @enderror>
            @error('admin_email')
                <p class="mt-1 text-base text-red-600 sm:text-sm"
                   id="admin_email-error">{{ $message }}</p>
            @enderror
        </div>

        <x-primary-button skip-permission
                          variant="login"
                          testId="register-submit-button"
                          :label="'Create workspace'" />
    </form>
@endsection
