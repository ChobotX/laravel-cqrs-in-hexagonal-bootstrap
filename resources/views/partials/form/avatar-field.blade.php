<div class="col-span-full"
     data-avatar-field>
    <label class="mb-1.5 block text-base font-medium text-gray-700 sm:text-sm"
           for="avatar">{{ __('messages.users.avatar') }}</label>
    <input name="remove_avatar"
           data-avatar-remove
           type="hidden"
           value="0">
    <div class="flex items-center gap-4">
        @if ($user->avatarFileId !== null)
            <div class="relative shrink-0"
                 data-avatar-preview>
                <img class="h-16 w-16 rounded-full object-cover ring-1 ring-gray-200"
                     src="{{ route('files.show', $user->avatarFileId->value) }}"
                     alt="{{ $user->name->value }}">
                <button class="absolute -right-1 -top-1 flex h-5 w-5 cursor-pointer items-center justify-center rounded-full bg-red-500 text-white shadow transition-colors hover:bg-red-600"
                        data-avatar-remove-btn
                        type="button"
                        title="{{ __('messages.users.avatar_remove') }}"
                        aria-label="{{ __('messages.users.avatar_remove') }}">
                    <x-heroicon-s-x-mark class="h-3 w-3" />
                </button>
            </div>
        @else
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700"
                 data-avatar-preview>
                {{ strtoupper(substr($user->name->value, 0, 1)) }}{{ strtoupper(substr($user->name->value, strpos($user->name->value, ' ') + 1, 1)) }}
            </div>
        @endif
        <input class="block w-full rounded-lg border border-gray-300 px-3.5 py-2 text-base text-gray-700 shadow-sm file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm"
               id="avatar"
               name="avatar"
               type="file"
               accept="image/*"
               @error('avatar') aria-describedby="avatar-error" aria-invalid="true" @enderror>
    </div>
    @error('avatar')
        <p class="mt-1 text-base text-red-600 sm:text-sm"
           id="avatar-error">{{ $message }}</p>
    @enderror
</div>
