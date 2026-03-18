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
            <p class="text-sm font-medium text-amber-800">
                {{ __('messages.impersonation.banner', ['name' => $impersonatedUser->name]) }}
            <form class="inline"
                  method="POST"
                  action="{{ route('impersonation.stop') }}">
                @csrf
                <x-icon-button class="font-semibold text-amber-900 underline transition-colors hover:text-amber-700"
                               skip-permission
                               icon="heroicon-o-x-mark"
                               :label="__('messages.impersonation.stop')" />
            </form>
            </p>
        </div>
    </div>
@endif
