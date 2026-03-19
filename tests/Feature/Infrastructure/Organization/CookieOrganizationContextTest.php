<?php

declare(strict_types=1);

use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Organization\OrganizationMembershipChecker;
use App\Infrastructure\Organization\CookieOrganizationContext;

function fakeAuth(?string $userId): AuthenticatedUser
{
    return new readonly class($userId) implements AuthenticatedUser
    {
        public function __construct(private ?string $userId) {}

        public function id(): ?string
        {
            return $this->userId;
        }

        public function impersonatorId(): ?string
        {
            return null;
        }

        public function isImpersonating(): bool
        {
            return false;
        }
    };
}

/** @param array<string, list<string>> $memberships */
function fakeMembership(array $memberships = []): OrganizationMembershipChecker
{
    return new readonly class($memberships) implements OrganizationMembershipChecker
    {
        /** @param array<string, list<string>> $memberships */
        public function __construct(private array $memberships) {}

        public function isMember(string $userId, string $organizationId): bool
        {
            return isset($this->memberships[$userId]) && in_array($organizationId, $this->memberships[$userId], true);
        }

        /** @return list<string> */
        public function memberOrganizationIds(string $userId): array
        {
            return $this->memberships[$userId] ?? [];
        }
    };
}

it('returns default org when user is not authenticated', function (): void {
    $context = new CookieOrganizationContext(
        organizationMembershipChecker: fakeMembership(),
        authenticatedUser: fakeAuth(null),
        defaultOrganizationId: 'default-org-id',
    );

    expect($context->currentOrganizationId())->toBe('default-org-id');
});

it('auto-selects when user has single organization', function (): void {
    $context = new CookieOrganizationContext(
        organizationMembershipChecker: fakeMembership(['user-1' => ['org-1']]),
        authenticatedUser: fakeAuth('user-1'),
    );

    expect($context->currentOrganizationId())->toBe('org-1');
});

it('returns default when user has multiple orgs and no cookie', function (): void {
    $context = new CookieOrganizationContext(
        organizationMembershipChecker: fakeMembership(['user-1' => ['org-1', 'org-2']]),
        authenticatedUser: fakeAuth('user-1'),
        defaultOrganizationId: 'fallback',
    );

    expect($context->currentOrganizationId())->toBe('fallback');
});

it('returns default when user has no memberships', function (): void {
    $context = new CookieOrganizationContext(
        organizationMembershipChecker: fakeMembership(),
        authenticatedUser: fakeAuth('user-1'),
        defaultOrganizationId: 'fallback',
    );

    expect($context->currentOrganizationId())->toBe('fallback');
});

it('resolves org from X-Organization-Id header', function (): void {
    $organizationMembershipChecker = fakeMembership(['user-1' => ['org-1', 'org-2']]);

    $request = Illuminate\Http\Request::create('/', 'GET', [], [], [], ['HTTP_X_ORGANIZATION_ID' => 'org-2']);
    app()->instance('request', $request);

    $context = new CookieOrganizationContext(
        organizationMembershipChecker: $organizationMembershipChecker,
        authenticatedUser: fakeAuth('user-1'),
    );

    expect($context->currentOrganizationId())->toBe('org-2');
});

it('falls back to default when header org is not a member', function (): void {
    $organizationMembershipChecker = fakeMembership(['user-1' => ['org-1']]);

    $request = Illuminate\Http\Request::create('/', 'GET', [], [], [], ['HTTP_X_ORGANIZATION_ID' => 'org-999']);
    app()->instance('request', $request);

    $context = new CookieOrganizationContext(
        organizationMembershipChecker: $organizationMembershipChecker,
        authenticatedUser: fakeAuth('user-1'),
    );

    expect($context->currentOrganizationId())->toBe('org-1');
});
