@if ($authenticatedUser?->isImpersonating())
    <div class="border-b border-amber-200 bg-amber-50"
         role="alert">
        <div class="flex items-center justify-between px-4 py-2">
            <span class="text-sm font-medium text-amber-800">
                {{ __('messages.impersonation.banner', ['name' => $authenticatedUser->name()]) }}
            </span>
            <x-primary-button skip-permission
                              variant="amber"
                              :action="route('impersonation.stop')"
                              :label="__('messages.impersonation.stop')" />
        </div>
    </div>
@endif
