@props([
    /** @var \DateTimeInterface $instant */
    'instant',
])
@php
    $iso = \App\Presentation\Http\Serialization\InstantJson::toRfc3339Utc($instant);
@endphp
<time data-local-datetime
      datetime="{{ $iso }}">{{ $iso }}</time>
