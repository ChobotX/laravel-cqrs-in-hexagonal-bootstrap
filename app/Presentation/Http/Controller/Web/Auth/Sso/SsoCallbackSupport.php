<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth\Sso;

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\Sso\Contract\Command\LoginViaSsoCommand;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\Service\SsoLoginSession;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;
use App\Presentation\Http\Request\Sso\SsoCallbackRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use function redirect;

/**
 * Shared login-completion flow for OIDC callback (GET) and SAML ACS (POST):
 * resolves the provider configuration by slug, dispatches LoginViaSsoCommand
 * with the raw IdP payload + CSRF `state`, and — once the domain handler has
 * established the resolved user — establishes the HTTP session via Auth.
 */
trait SsoCallbackSupport
{
    private function completeLogin(SsoCallbackRequest $ssoCallbackRequest, string $slug, CommandBus $commandBus, QueryBus $queryBus, IdGenerator $idGenerator, SsoLoginSession $ssoLoginSession): RedirectResponse
    {
        $configurationId = $this->resolveConfigurationId($slug, $queryBus);

        $commandBus->dispatch(new LoginViaSsoCommand(
            configurationId: $configurationId,
            callbackPayload: $ssoCallbackRequest->payload(),
            state: $ssoCallbackRequest->stateValue(),
            newUserIdIfProvisioned: $idGenerator->generate(),
            newIdentityId: $idGenerator->generate(),
        ));

        $userId = $ssoLoginSession->pullLastResolvedUserId();

        if ($userId === null) {
            $ssoLoginSession->clear();

            throw new SsoLoginRejectedException('no_resolved_user');
        }

        Auth::loginUsingId($userId);

        return redirect()->intended('/users');
    }

    private function resolveConfigurationId(string $slug, QueryBus $queryBus): string
    {
        /** @var list<EnabledSsoProvider> $providers */
        $providers = $queryBus->dispatch(new GetEnabledSsoProvidersQuery);

        foreach ($providers as $provider) {
            if ($provider->slug === $slug) {
                return $provider->configurationId;
            }
        }

        throw new SsoConfigurationNotFoundException($slug);
    }
}
