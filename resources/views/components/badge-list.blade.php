@props(['labels', 'max' => 2])

@if (!empty($labels))
    @foreach ($labels as $label)
        @if ($loop->index < $max)
            <span
                  class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600 ring-1 ring-gray-500/10">{{ $label->name->value }}</span>
        @endif
    @endforeach
    @if (count($labels) > $max)
        <x-tooltip>
            <span
                  class="inline-flex cursor-default items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600 ring-1 ring-gray-500/10">+{{ count($labels) - $max }}</span>
            <x-slot:content>
                <span class="flex flex-wrap gap-1">
                    @foreach ($labels as $label)
                        <span
                              class="inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-medium text-white">{{ $label->name->value }}</span>
                    @endforeach
                </span>
            </x-slot:content>
        </x-tooltip>
    @endif
@endif
