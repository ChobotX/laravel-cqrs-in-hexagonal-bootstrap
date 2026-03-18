@if (session('success') || session('error'))
    <div id="app-flash-data"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         hidden>
    </div>
@endif
