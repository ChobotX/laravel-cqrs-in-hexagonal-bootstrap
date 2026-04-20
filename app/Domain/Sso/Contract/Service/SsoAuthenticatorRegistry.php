<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Service;

use App\Domain\Sso\Contract\Enum\ProviderType;

/**
 * Resolves the SsoAuthenticator adapter implementing a given ProviderType.
 *
 * Implementation lives in `App\Infrastructure\Sso\SsoAuthenticatorRegistry`.
 */
interface SsoAuthenticatorRegistry
{
    public function for(ProviderType $providerType): SsoAuthenticator;
}
