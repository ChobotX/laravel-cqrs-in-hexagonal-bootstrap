<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Organization\OrganizationContext;
use App\Contract\Organization\OrganizationMembershipChecker;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use RuntimeException;

final readonly class CookieOrganizationContext implements OrganizationContext
{
    private const string COOKIE_NAME = 'organization_id';

    public function __construct(
        private OrganizationMembershipChecker $organizationMembershipChecker,
        private AuthenticatedUser $authenticatedUser,
        private ?string $defaultOrganizationId = null,
    ) {}

    public function currentOrganizationId(): string
    {
        $userId = $this->authenticatedUser->id();

        if ($userId === null) {
            return $this->defaultOrganizationId ?? throw new RuntimeException(
                'Organization context requires a default organization ID for unauthenticated requests.',
            );
        }

        $headerValue = Request::header('X-Organization-Id');
        $fromHeader = is_string($headerValue) ? $headerValue : null;
        $fromCookie = Cookie::get(self::COOKIE_NAME);
        $candidateId = $fromHeader ?? (is_string($fromCookie) ? $fromCookie : null);

        if ($candidateId !== null && $this->organizationMembershipChecker->isMember($userId, $candidateId)) {
            return $candidateId;
        }

        $orgIds = $this->organizationMembershipChecker->memberOrganizationIds($userId);

        return $orgIds[0] ?? $this->defaultOrganizationId ?? throw new RuntimeException(
            'Authenticated user has no organization memberships and no default organization is configured.',
        );
    }
}
