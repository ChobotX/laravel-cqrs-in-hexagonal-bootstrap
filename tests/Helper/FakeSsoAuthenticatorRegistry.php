<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry;

final class FakeSsoAuthenticatorRegistry implements SsoAuthenticatorRegistry
{
    public function __construct(
        public FakeSsoAuthenticator $authenticator = new FakeSsoAuthenticator,
    ) {}

    public function for(ProviderType $providerType): SsoAuthenticator
    {
        return $this->authenticator;
    }
}
