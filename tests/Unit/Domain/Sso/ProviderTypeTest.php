<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Enum\ProviderType;

it('flags social providers', function (ProviderType $providerType, bool $expected): void {
    expect($providerType->isOauthSocial())->toBe($expected);
})->with([
    [ProviderType::Google, true],
    [ProviderType::Microsoft, true],
    [ProviderType::Github, true],
    [ProviderType::Oidc, false],
    [ProviderType::Saml, false],
]);
