<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use App\Infrastructure\Eloquent\Sso\SsoConfigurationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Tests\Helper\FakeSsoAuthenticator;
use Tests\Helper\FakeSsoAuthenticatorRegistry;

beforeEach(function (): void {
    $model = new SsoConfigurationModel;
    $model->id = '11111111-1111-1111-1111-111111111111';
    $model->fill([
        'provider_type' => 'oidc',
        'slug' => 'primary',
        'display_name' => 'Primary OIDC',
        'enabled' => true,
        'enforce' => false,
        'jit_mode' => 'invited_only',
        'allowed_email_domains' => [],
        'config' => ['client_id' => 'cid'],
    ]);
    $model->save();
});

function bindFakeAuthenticator(FakeSsoAuthenticator $fakeSsoAuthenticator): void
{
    app()->instance(SsoAuthenticatorRegistry::class, new FakeSsoAuthenticatorRegistry($fakeSsoAuthenticator));
}

it('redirects to the IdP via initiate route', function (): void {
    bindFakeAuthenticator(new FakeSsoAuthenticator(
        nextRedirect: new RedirectInstruction('https://idp.example.com/auth?abc'),
    ));

    $this->get('/auth/sso/primary')->assertRedirect('https://idp.example.com/auth?abc');
});

it('renders the post-binding form when SAML responds with one', function (): void {
    bindFakeAuthenticator(new FakeSsoAuthenticator(
        nextRedirect: new RedirectInstruction('https://idp.example.com/sso', usesPostBinding: true, formFields: ['SAMLRequest' => 'abc']),
    ));

    $this->get('/auth/sso/primary')
        ->assertOk()
        ->assertSee('SAMLRequest', false);
});

it('rejects an unknown slug', function (): void {
    $this->get('/auth/sso/missing')->assertStatus(404);
});

it('completes the OAuth callback for an existing user with linked identity', function (): void {
    UserModel::create([
        'id' => '22222222-2222-2222-2222-222222222222',
        'name' => 'User',
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
    ]);

    DB::connection('tenant')->table('user_sso_identities')->insert([
        'id' => '33333333-3333-3333-3333-333333333333',
        'user_id' => '22222222-2222-2222-2222-222222222222',
        'configuration_id' => '11111111-1111-1111-1111-111111111111',
        'subject' => 'subject-1',
        'email_at_link' => 'user@example.com',
        'linked_at' => now(),
    ]);

    bindFakeAuthenticator(new FakeSsoAuthenticator(
        nextIdentity: new SsoIdentity('subject-1', 'user@example.com', 'User'),
    ));

    $this->get('/auth/sso/primary/callback?code=abc')
        ->assertRedirect('/users');

    expect(auth()->id())->toBe('22222222-2222-2222-2222-222222222222');
});

it('rejects an OAuth callback for an unknown slug', function (): void {
    $this->get('/auth/sso/missing/callback?code=abc')->assertStatus(404);
});

it('completes the SAML ACS for an existing linked user', function (): void {
    UserModel::create([
        'id' => '22222222-2222-2222-2222-222222222222',
        'name' => 'User',
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
    ]);

    DB::connection('tenant')->table('user_sso_identities')->insert([
        'id' => '33333333-3333-3333-3333-333333333333',
        'user_id' => '22222222-2222-2222-2222-222222222222',
        'configuration_id' => '11111111-1111-1111-1111-111111111111',
        'subject' => 'saml-sub',
        'email_at_link' => 'user@example.com',
        'linked_at' => now(),
    ]);

    bindFakeAuthenticator(new FakeSsoAuthenticator(
        nextIdentity: new SsoIdentity('saml-sub', 'user@example.com', 'User'),
    ));

    $this->post('/auth/sso/saml/primary/acs', ['SAMLResponse' => 'fake'])
        ->assertRedirect('/users');
});

it('rejects SAML ACS for an unknown slug', function (): void {
    $this->post('/auth/sso/saml/missing/acs', ['SAMLResponse' => 'x'])->assertStatus(404);
});

it('returns SAML metadata for SAML configurations', function (): void {
    SsoConfigurationModel::query()->find('11111111-1111-1111-1111-111111111111')->update([
        'provider_type' => 'saml',
        'slug' => 'saml-primary',
        'config' => [
            'sp' => [
                'entityId' => 'https://app.example.com/metadata',
                'assertionConsumerService' => ['url' => 'https://app.example.com/auth/sso/saml/saml-primary/acs'],
            ],
        ],
    ]);

    $this->get('/auth/sso/saml/saml-primary/metadata')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('https://app.example.com/metadata', false);
});

it('rejects SAML metadata for unknown slug', function (): void {
    $this->get('/auth/sso/saml/missing/metadata')->assertStatus(404);
});

it('blocks password login when an SSO configuration enforces it', function (): void {
    SsoConfigurationModel::query()->find('11111111-1111-1111-1111-111111111111')->update(['enforce' => true]);

    $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password123'])
        ->assertSessionHasErrors('email');
});

it('shows enabled SSO providers on the login page when feature flag is on', function (): void {
    DB::connection('tenant')->table('feature_flag_overrides')->insertOrIgnore([
        'key' => 'sso.enabled',
        'value' => '1',
        'enabled' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    cache()->flush();

    $this->get('/login')->assertOk()->assertSee('Primary OIDC');
});

it('returns the connection test summary via the admin endpoint', function (): void {
    bindFakeAuthenticator(new FakeSsoAuthenticator(
        nextProbe: new SsoConnectionTestResult(true, 'OK'),
    ));

    $this->seedSuperAdminRole();
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f20',
        'name' => 'Admin',
        'email' => 'admin2@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($admin->id);

    $this->actingAs($admin)
        ->post('/settings/sso/11111111-1111-1111-1111-111111111111/test')
        ->assertRedirect('/settings/sso');
});
