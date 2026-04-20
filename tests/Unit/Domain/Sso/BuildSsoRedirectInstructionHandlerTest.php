<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Query\BuildSsoRedirectInstructionQuery;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Handler\Query\BuildSsoRedirectInstructionHandler;
use Tests\Helper\FakeSsoAuthenticator;
use Tests\Helper\FakeSsoAuthenticatorRegistry;
use Tests\Helper\FakeSsoConfigurationRepository;
use Tests\Helper\SsoFixtures;

it('returns the IdP redirect for an enabled configuration', function (): void {
    $configuration = SsoFixtures::configuration();
    $repository = new FakeSsoConfigurationRepository([$configuration->id->value => $configuration]);
    $authenticator = new FakeSsoAuthenticator(nextRedirect: new RedirectInstruction('https://idp.example.com/auth'));

    $redirectInstruction = new BuildSsoRedirectInstructionHandler($repository, new FakeSsoAuthenticatorRegistry($authenticator))
        ->handle(new BuildSsoRedirectInstructionQuery($configuration->slug));

    expect($redirectInstruction->url)->toBe('https://idp.example.com/auth');
});

it('rejects an unknown slug', function (): void {
    new BuildSsoRedirectInstructionHandler(new FakeSsoConfigurationRepository, new FakeSsoAuthenticatorRegistry)
        ->handle(new BuildSsoRedirectInstructionQuery('missing'));
})->throws(SsoConfigurationNotFoundException::class);
