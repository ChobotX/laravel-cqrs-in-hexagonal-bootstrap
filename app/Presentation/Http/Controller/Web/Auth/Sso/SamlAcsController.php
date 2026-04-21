<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth\Sso;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\Sso\Contract\Service\SsoLoginSession;
use App\Presentation\Http\Request\Sso\SsoCallbackRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('SAML POST-binding ACS endpoint; identity is asserted by the IdP signature.')]
final readonly class SamlAcsController
{
    use SsoCallbackSupport;

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private IdGenerator $idGenerator,
        private SsoLoginSession $ssoLoginSession,
    ) {}

    public function __invoke(SsoCallbackRequest $ssoCallbackRequest, string $slug): RedirectResponse
    {
        return $this->completeLogin($ssoCallbackRequest, $slug, $this->commandBus, $this->queryBus, $this->idGenerator, $this->ssoLoginSession);
    }
}
