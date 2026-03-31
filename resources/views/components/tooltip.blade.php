@props(['text' => null, 'position' => 'top'])

<span class="inline-flex"
      data-tooltip-position="{{ $position }}"
      @if ($text !== null) data-tooltip="{{ $text }}" @else data-tooltip @endif>
    {{ $slot }}
    @isset($content)
        <template data-tooltip-content>{{ $content }}</template>
    @endisset
</span>
