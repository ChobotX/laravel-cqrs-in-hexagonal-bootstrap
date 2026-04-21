<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\BuildSsoRedirectInstructionQuery;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry;
use App\Domain\Sso\Contract\Service\SsoLoginSession;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;

use function array_find;

/** @implements QueryHandler<BuildSsoRedirectInstructionQuery, RedirectInstruction> */
final readonly class BuildSsoRedirectInstructionHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private SsoAuthenticatorRegistry $ssoAuthenticatorRegistry,
        private SsoLoginSession $ssoLoginSession,
    ) {}

    public function handle(Query $query): RedirectInstruction
    {
        $configuration = array_find(
            $this->ssoConfigurationRepository->allEnabled(),
            fn (SsoConfiguration $ssoConfiguration): bool => $ssoConfiguration->slug === $query->slug,
        );

        if (! $configuration instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($query->slug);
        }

        $redirectInstruction = $this->ssoAuthenticatorRegistry->for($configuration->providerType)->initiate($configuration);

        if ($redirectInstruction->stateToStore !== null) {
            $this->ssoLoginSession->rememberHandshake(
                slug: $configuration->slug,
                state: $redirectInstruction->stateToStore,
                nonce: $redirectInstruction->nonceToStore,
            );
        }

        return $redirectInstruction;
    }
}
