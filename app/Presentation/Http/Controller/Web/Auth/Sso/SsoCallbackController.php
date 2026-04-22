<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Auth\Sso;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Bus\QueryBus;
use App\Contract\IdGenerator;
use App\Domain\Sso\Contract\Service\SsoLoginSession;
use App\Presentation\Http\Request\Sso\SsoCallbackRequest;
use Illuminate\Http\RedirectResponse;

#[SkipPermissionCheck('IdP callback; identity is asserted by the configured authenticator.')]
final readonly class SsoCallbackController
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
