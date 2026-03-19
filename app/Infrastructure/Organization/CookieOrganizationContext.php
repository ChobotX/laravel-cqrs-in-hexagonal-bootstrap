<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Organization\OrganizationContext;
use App\Contract\Organization\OrganizationMembershipChecker;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

final readonly class CookieOrganizationContext implements OrganizationContext
{
    private const string COOKIE_NAME = 'organization_id';

    public function __construct(
        private OrganizationMembershipChecker $organizationMembershipChecker,
        private AuthenticatedUser $authenticatedUser,
        private ?string $defaultOrganizationId = null,
    ) {}

    public function currentOrganizationId(): ?string
    {
        $userId = $this->authenticatedUser->id();

        if ($userId === null) {
            return $this->defaultOrganizationId;
        }

        $headerValue = Request::header('X-Organization-Id');
        $fromHeader = is_string($headerValue) ? $headerValue : null;
        $fromCookie = Cookie::get(self::COOKIE_NAME);
        $candidateId = $fromHeader ?? (is_string($fromCookie) ? $fromCookie : null);

        if ($candidateId !== null && $this->organizationMembershipChecker->isMember($userId, $candidateId)) {
            return $candidateId;
        }

        $orgIds = $this->organizationMembershipChecker->memberOrganizationIds($userId);

        if (count($orgIds) === 1) {
            return $orgIds[0];
        }

        return $this->defaultOrganizationId;
    }
}
