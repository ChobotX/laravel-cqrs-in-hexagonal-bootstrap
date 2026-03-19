@php
    $authenticatedUser = app(\App\Contract\Auth\AuthenticatedUser::class);
    $userId = $authenticatedUser->id();
    $organizations = [];
    $currentOrgId = null;

    if ($userId !== null) {
        $queryBus = app(\App\Application\Bus\QueryBus::class);
        $organizationContext = app(\App\Contract\Organization\OrganizationContext::class);
        $currentOrgId = $organizationContext->currentOrganizationId();
        $organizations = $queryBus->dispatch(
            new \App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsQuery($userId),
        );
    }
@endphp

@if (count($organizations) > 1)
    <div class="border-b border-white/10 px-3 py-3">
        <label class="sr-only"
               for="org-switcher">{{ __('messages.organizations.switch') }}</label>
        <form method="POST"
              action="{{ route('organizations.switch') }}">
            @csrf
            <select class="w-full rounded-lg border-gray-600 bg-gray-900 px-3 py-2 text-sm text-white focus:border-indigo-500 focus:ring-indigo-500"
                    id="org-switcher"
                    name="organization_id"
                    data-auto-submit>
                @foreach ($organizations as $org)
                    <option value="{{ $org->id }}"
                            @if ((string) $org->id === $currentOrgId) selected @endif>
                        {{ $org->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
@endif
