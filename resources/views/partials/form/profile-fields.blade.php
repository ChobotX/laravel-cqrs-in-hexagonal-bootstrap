<div>
    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
           for="name">{{ __('messages.users.name') }}</label>
    <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
           id="name"
           name="name"
           type="text"
           value="{{ old('name', $user->name->value) }}"
           required
           @error('name') aria-describedby="name-error" aria-invalid="true" @enderror>
    @error('name')
        <p class="mt-1 text-base text-red-600 sm:text-sm"
           id="name-error">{{ $message }}</p>
    @enderror
</div>

@if ($canEditEmail ?? true)
    <div>
        <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
               for="email">{{ __('messages.users.email') }}</label>
        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
               id="email"
               name="email"
               type="email"
               value="{{ old('email', $user->email) }}"
               required
               @error('email') aria-describedby="email-error" aria-invalid="true" @enderror>
        @error('email')
            <p class="mt-1 text-base text-red-600 sm:text-sm"
               id="email-error">{{ $message }}</p>
        @enderror
    </div>
@endif
