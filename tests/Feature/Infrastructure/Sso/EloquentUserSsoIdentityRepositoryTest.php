<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Infrastructure\Eloquent\Sso\EloquentSsoConfigurationRepository;
use App\Infrastructure\Eloquent\Sso\EloquentUserSsoIdentityRepository;
use App\Infrastructure\Eloquent\Sso\SsoConfigurationMapper;
use App\Infrastructure\Eloquent\Sso\UserSsoIdentityMapper;
use Illuminate\Support\Facades\DB;

function userSsoIdentityRepo(): EloquentUserSsoIdentityRepository
{
    return new EloquentUserSsoIdentityRepository(new UserSsoIdentityMapper);
}

function configRepoForIdentities(): EloquentSsoConfigurationRepository
{
    return new EloquentSsoConfigurationRepository(new SsoConfigurationMapper);
}

function seedUserRow(string $userId, string $email): void
{
    DB::connection('tenant')->table('users')->insert([
        'id' => $userId,
        'name' => 'Existing',
        'email' => $email,
        'password' => 'not-a-real-hash',
        'lock_version' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

function makeConfigForIdentitiesTest(string $id): SsoConfiguration
{
    $now = new DateTimeImmutable;

    return new SsoConfiguration(
        id: new SsoConfigurationId($id),
        providerType: ProviderType::Oidc,
        slug: 'primary',
        displayName: 'Primary',
        enabled: true,
        enforce: false,
        jitMode: JitMode::InvitedOnly,
        allowedEmailDomains: new AllowedEmailDomains([]),
        config: [],
        createdAt: $now,
        updatedAt: $now,
    );
}

function identity(string $id, string $userId, string $configurationId, string $subject = 'subject-1'): UserSsoIdentity
{
    return new UserSsoIdentity(
        id: new UserSsoIdentityId($id),
        userId: new UserId($userId),
        configurationId: new SsoConfigurationId($configurationId),
        subject: $subject,
        emailAtLink: 'user@example.com',
        linkedAt: new DateTimeImmutable,
    );
}

it('persists, looks up, lists, and deletes identities', function (): void {
    $eloquentSsoConfigurationRepository = configRepoForIdentities();
    $eloquentUserSsoIdentityRepository = userSsoIdentityRepo();

    $eloquentSsoConfigurationRepository->create(makeConfigForIdentitiesTest('11111111-1111-1111-1111-111111111111'));
    seedUserRow('22222222-2222-2222-2222-222222222222', 'user@example.com');

    $identity = identity('33333333-3333-3333-3333-333333333333', '22222222-2222-2222-2222-222222222222', '11111111-1111-1111-1111-111111111111');
    $eloquentUserSsoIdentityRepository->create($identity);

    expect($eloquentUserSsoIdentityRepository->findById($identity->id)?->subject)->toBe('subject-1')
        ->and($eloquentUserSsoIdentityRepository->findBySubject($identity->configurationId, 'subject-1')?->id->value)->toBe($identity->id->value)
        ->and($eloquentUserSsoIdentityRepository->findBySubject($identity->configurationId, 'other'))->toBeNull()
        ->and($eloquentUserSsoIdentityRepository->listForUser($identity->userId))->toHaveCount(1);

    $eloquentUserSsoIdentityRepository->delete($identity->id);
    expect($eloquentUserSsoIdentityRepository->findById($identity->id))->toBeNull();
});

it('deletes all identities linked to a configuration', function (): void {
    $eloquentSsoConfigurationRepository = configRepoForIdentities();
    $eloquentUserSsoIdentityRepository = userSsoIdentityRepo();

    $eloquentSsoConfigurationRepository->create(makeConfigForIdentitiesTest('11111111-1111-1111-1111-111111111111'));
    seedUserRow('22222222-2222-2222-2222-222222222222', 'user@example.com');

    $eloquentUserSsoIdentityRepository->create(identity('33333333-3333-3333-3333-333333333333', '22222222-2222-2222-2222-222222222222', '11111111-1111-1111-1111-111111111111', 'subject-a'));
    $eloquentUserSsoIdentityRepository->create(identity('44444444-4444-4444-4444-444444444444', '22222222-2222-2222-2222-222222222222', '11111111-1111-1111-1111-111111111111', 'subject-b'));

    $eloquentUserSsoIdentityRepository->deleteAllForConfiguration(new SsoConfigurationId('11111111-1111-1111-1111-111111111111'));

    expect($eloquentUserSsoIdentityRepository->listForUser(new UserId('22222222-2222-2222-2222-222222222222')))->toBeEmpty();
});

it('returns null for unknown identity id', function (): void {
    expect(userSsoIdentityRepo()->findById(new UserSsoIdentityId('99999999-9999-9999-9999-999999999999')))->toBeNull();
});
