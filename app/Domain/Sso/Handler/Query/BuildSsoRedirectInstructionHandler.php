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
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;

/** @implements QueryHandler<BuildSsoRedirectInstructionQuery, RedirectInstruction> */
final readonly class BuildSsoRedirectInstructionHandler implements QueryHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private SsoAuthenticatorRegistry $ssoAuthenticatorRegistry,
    ) {}

    public function handle(Query $query): RedirectInstruction
    {
        $configuration = array_find($this->ssoConfigurationRepository->allEnabled(), fn ($ssoConfiguration): bool => $ssoConfiguration->slug === $query->slug);
        if (! $configuration instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($query->slug);
        }

        return $this->ssoAuthenticatorRegistry->for($configuration->providerType)->initiate($configuration);
    }
}
