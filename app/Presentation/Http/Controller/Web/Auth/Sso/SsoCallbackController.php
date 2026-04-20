<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth\Sso;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\Sso\Contract\Command\LoginViaSsoCommand;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\GetEnabledSsoProvidersQuery;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;
use App\Presentation\Http\Request\Sso\SsoCallbackRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function is_string;
use function redirect;

#[SkipPermissionCheck('IdP callback; identity is asserted by the configured authenticator.')]
final readonly class SsoCallbackController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(SsoCallbackRequest $ssoCallbackRequest, string $slug): RedirectResponse
    {
        $configurationId = $this->resolveConfigurationId($slug);

        $this->commandBus->dispatch(new LoginViaSsoCommand(
            configurationId: $configurationId,
            callbackPayload: $ssoCallbackRequest->payload(),
            newUserIdIfProvisioned: $this->idGenerator->generate(),
            newIdentityId: $this->idGenerator->generate(),
        ));

        $userId = Session::pull('sso.last_resolved_user_id');
        Auth::loginUsingId(is_string($userId) ? $userId : '');

        return redirect()->intended('/users');
    }

    private function resolveConfigurationId(string $slug): string
    {
        /** @var list<EnabledSsoProvider> $providers */
        $providers = $this->queryBus->dispatch(new GetEnabledSsoProvidersQuery);

        foreach ($providers as $provider) {
            if ($provider->slug === $slug) {
                return $provider->configurationId;
            }
        }

        throw new SsoConfigurationNotFoundException($slug);
    }
}
