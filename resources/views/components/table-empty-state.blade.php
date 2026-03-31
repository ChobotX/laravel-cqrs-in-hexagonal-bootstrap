@props(['colspan', 'message'])

<tr>
    <td class="px-6 py-12 text-center"
        colspan="{{ $colspan }}">
        <div class="flex flex-col items-center gap-2">
            <x-heroicon-o-inbox class="size-8 text-gray-300" />
            <p class="text-sm text-gray-500">{{ $message }}</p>
        </div>
    </td>
</tr>
