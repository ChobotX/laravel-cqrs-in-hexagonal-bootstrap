<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Infrastructure\Eloquent\Sso\EloquentSsoConfigurationRepository;
use App\Infrastructure\Eloquent\Sso\SsoConfigurationMapper;

function ssoConfigurationRepo(): EloquentSsoConfigurationRepository
{
    return new EloquentSsoConfigurationRepository(new SsoConfigurationMapper);
}

function makeSsoConfiguration(string $id, string $slug = 'primary', bool $enabled = true, bool $enforce = false): SsoConfiguration
{
    $now = new DateTimeImmutable;

    return new SsoConfiguration(
        id: new SsoConfigurationId($id),
        providerType: ProviderType::Oidc,
        slug: $slug,
        displayName: 'Primary OIDC',
        enabled: $enabled,
        enforce: $enforce,
        jitMode: JitMode::InvitedOnly,
        allowedEmailDomains: new AllowedEmailDomains(['acme.com']),
        config: ['client_id' => 'cid', 'client_secret' => 'secret'],
        createdAt: $now,
        updatedAt: $now,
    );
}

it('persists and reloads a configuration', function (): void {
    $eloquentSsoConfigurationRepository = ssoConfigurationRepo();
    $ssoConfiguration = makeSsoConfiguration('11111111-1111-1111-1111-111111111111');

    $eloquentSsoConfigurationRepository->create($ssoConfiguration);
    $found = $eloquentSsoConfigurationRepository->findById($ssoConfiguration->id);

    expect($found)->not->toBeNull()
        ->and($found->slug)->toBe('primary')
        ->and($found->config['client_secret'])->toBe('secret');
});

it('returns null for missing id', function (): void {
    expect(ssoConfigurationRepo()->findById(new SsoConfigurationId('22222222-2222-2222-2222-222222222222')))->toBeNull();
});

it('lists all configurations', function (): void {
    $eloquentSsoConfigurationRepository = ssoConfigurationRepo();
    $eloquentSsoConfigurationRepository->create(makeSsoConfiguration('11111111-1111-1111-1111-111111111111', slug: 'a'));
    $eloquentSsoConfigurationRepository->create(makeSsoConfiguration('33333333-3333-3333-3333-333333333333', slug: 'b', enabled: false));

    expect($eloquentSsoConfigurationRepository->all())->toHaveCount(2);
});

it('lists only enabled configurations', function (): void {
    $eloquentSsoConfigurationRepository = ssoConfigurationRepo();
    $eloquentSsoConfigurationRepository->create(makeSsoConfiguration('11111111-1111-1111-1111-111111111111', slug: 'a'));
    $eloquentSsoConfigurationRepository->create(makeSsoConfiguration('33333333-3333-3333-3333-333333333333', slug: 'b', enabled: false));

    expect($eloquentSsoConfigurationRepository->allEnabled())->toHaveCount(1);
});

it('finds a configuration by provider type and slug', function (): void {
    $eloquentSsoConfigurationRepository = ssoConfigurationRepo();
    $ssoConfiguration = makeSsoConfiguration('11111111-1111-1111-1111-111111111111');
    $eloquentSsoConfigurationRepository->create($ssoConfiguration);

    expect($eloquentSsoConfigurationRepository->findBySlug(ProviderType::Oidc, 'primary'))->not->toBeNull()
        ->and($eloquentSsoConfigurationRepository->findBySlug(ProviderType::Oidc, 'missing'))->toBeNull();
});

it('reports whether any enforced configuration exists', function (): void {
    $eloquentSsoConfigurationRepository = ssoConfigurationRepo();

    expect($eloquentSsoConfigurationRepository->hasEnforcedConfiguration())->toBeFalse();

    $eloquentSsoConfigurationRepository->create(makeSsoConfiguration('11111111-1111-1111-1111-111111111111', enforce: true));

    expect($eloquentSsoConfigurationRepository->hasEnforcedConfiguration())->toBeTrue();
});

it('updates and deletes a configuration', function (): void {
    $eloquentSsoConfigurationRepository = ssoConfigurationRepo();
    $ssoConfiguration = makeSsoConfiguration('11111111-1111-1111-1111-111111111111');
    $eloquentSsoConfigurationRepository->create($ssoConfiguration);

    $updated = new SsoConfiguration(
        id: $ssoConfiguration->id,
        providerType: $ssoConfiguration->providerType,
        slug: $ssoConfiguration->slug,
        displayName: 'Renamed',
        enabled: false,
        enforce: false,
        jitMode: JitMode::AutoCreate,
        allowedEmailDomains: new AllowedEmailDomains([]),
        config: [],
        createdAt: $ssoConfiguration->createdAt,
        updatedAt: new DateTimeImmutable,
    );

    $eloquentSsoConfigurationRepository->update($updated);
    expect($eloquentSsoConfigurationRepository->findById($ssoConfiguration->id)?->displayName)->toBe('Renamed');

    $eloquentSsoConfigurationRepository->delete($ssoConfiguration->id);
    expect($eloquentSsoConfigurationRepository->findById($ssoConfiguration->id))->toBeNull();
});
