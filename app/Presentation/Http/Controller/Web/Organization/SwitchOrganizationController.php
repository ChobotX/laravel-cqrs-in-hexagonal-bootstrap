<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Organization\OrganizationMembershipChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

#[SkipPermissionCheck(reason: 'Every authenticated user can switch between their own organizations')]
final readonly class SwitchOrganizationController
{
    public function __construct(
        private AuthenticatedUser $authenticatedUser,
        private OrganizationMembershipChecker $organizationMembershipChecker,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $organizationId = $request->string('organization_id')->toString();
        $userId = $this->authenticatedUser->id();

        if ($userId !== null && $this->organizationMembershipChecker->isMember($userId, $organizationId)) {
            $cookie = Cookie::make('organization_id', $organizationId, 60 * 24 * 30);

            return redirect()->back()->withCookie($cookie);
        }

        return redirect()->back();
    }
}
