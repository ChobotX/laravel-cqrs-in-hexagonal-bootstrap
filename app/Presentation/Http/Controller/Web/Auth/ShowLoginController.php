<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\QueryBus;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;
use App\Presentation\Http\Request\Auth\ShowLoginRequest;
use App\Presentation\Http\Security\SafeRedirectValidator;
use Illuminate\View\View;

#[SkipPermissionCheck('Guest login page')]
final readonly class ShowLoginController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(ShowLoginRequest $showLoginRequest): View
    {
        $redirect = $showLoginRequest->validated('redirect');

        if (is_string($redirect) && SafeRedirectValidator::isSafe($redirect, $showLoginRequest->getHost())) {
            $showLoginRequest->session()->put('url.intended', $redirect);
        }

        /** @var list<EnabledSsoProvider> $ssoProviders */
        $ssoProviders = $this->queryBus->dispatch(new GetEnabledSsoProvidersQuery);

        return view('auth.login', ['ssoProviders' => $ssoProviders]);
    }
}
