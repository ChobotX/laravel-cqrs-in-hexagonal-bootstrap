@php
    $authenticatedUser = app(\App\Contract\Auth\AuthenticatedUser::class);
@endphp

@if ($authenticatedUser->isImpersonating())
    @php
        $impersonatedUser = app(\App\Application\Bus\QueryBus::class)->dispatch(
            new \App\Domain\User\Query\GetUserById\GetUserByIdQuery($authenticatedUser->id()),
        );
    @endphp
    <div class="border-b border-amber-200 bg-amber-50"
         role="alert">
        <div class="flex items-center justify-between px-4 py-2">
            <span class="text-sm font-medium text-amber-800">
                {{ __('messages.impersonation.banner', ['name' => $impersonatedUser->name]) }}
            </span>
            <x-primary-button skip-permission
                              variant="amber"
                              :action="route('impersonation.stop')"
                              :label="__('messages.impersonation.stop')" />
        </div>
    </div>
@endif
