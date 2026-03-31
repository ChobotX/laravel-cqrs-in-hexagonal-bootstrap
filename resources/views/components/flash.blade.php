@php
    $flashSuccess = session('success');
    $flashError = session('error');

    if (request()->query('csrf_expired')) {
        $flashError = __('messages.auth.session_expired');
    }

    if (! $flashError && $errors->any()) {
        $flashError = $errors->first();
    }
@endphp

@if ($flashSuccess || $flashError)
    <div id="app-flash-data"
         data-success="{{ $flashSuccess }}"
         data-error="{{ $flashError }}"
         hidden>
    </div>
@endif
