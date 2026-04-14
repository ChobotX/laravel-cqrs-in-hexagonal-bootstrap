@php
    $rotation = session('password_rotation');
@endphp

@if ($rotation === 'warning')
    <div class="mb-4 rounded-md border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900"
         role="status">
        {{ __('messages.settings.password_rotation_warning') }}
        <a class="font-medium text-amber-950 underline"
           href="{{ route('profile') }}"
           title="{{ __('messages.settings.change_password_link') }}">{{ __('messages.settings.change_password_link') }}</a>
    </div>
@endif

@if ($rotation === 'expired')
    <div class="mb-4 rounded-md border border-red-300 bg-red-50 p-4 text-sm text-red-900"
         role="alert">
        {{ __('messages.settings.password_rotation_expired') }}
        <a class="font-medium text-red-950 underline"
           href="{{ route('profile') }}"
           title="{{ __('messages.settings.change_password_link') }}">{{ __('messages.settings.change_password_link') }}</a>
    </div>
@endif
