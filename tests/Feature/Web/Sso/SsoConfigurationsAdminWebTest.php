<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Sso\SsoConfigurationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    $this->seedSuperAdminRole();
    $this->admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f10',
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($this->admin->id);

    $this->guest = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f11',
        'name' => 'Regular',
        'email' => 'regular@example.com',
        'password' => Hash::make('password123'),
    ]);
});

function seedSsoConfiguration(string $id = '11111111-1111-1111-1111-111111111111', string $slug = 'primary', bool $enabled = true): SsoConfigurationModel
{
    $model = new SsoConfigurationModel;
    $model->id = $id;
    $model->fill([
        'provider_type' => 'oidc',
        'slug' => $slug,
        'display_name' => 'Primary OIDC',
        'enabled' => $enabled,
        'enforce' => false,
        'jit_mode' => 'invited_only',
        'allowed_email_domains' => [],
        'config' => ['client_id' => 'cid'],
    ]);
    $model->save();

    return $model;
}

it('lists configurations for super admin', function (): void {
    seedSsoConfiguration();

    $this->actingAs($this->admin)->get('/settings/sso')->assertOk()->assertSee('Primary OIDC');
});

it('forbids non-admin from listing', function (): void {
    $this->actingAs($this->guest)->get('/settings/sso')->assertStatus(403);
});

it('shows the create form', function (): void {
    $this->actingAs($this->admin)->get('/settings/sso/create')->assertOk()->assertSee('Create');
});

it('stores a new configuration', function (): void {
    $this->actingAs($this->admin)->post('/settings/sso', [
        'provider_type' => 'oidc',
        'slug' => 'okta',
        'display_name' => 'Okta',
        'enabled' => 1,
        'enforce' => 0,
        'jit_mode' => 'invited_only',
        'allowed_email_domains' => 'acme.com,partner.com',
        'config' => ['client_id' => 'cid', 'client_secret' => 'secret'],
    ])->assertRedirect('/settings/sso');

    expect(SsoConfigurationModel::query()->where('slug', 'okta')->exists())->toBeTrue();
});

it('shows the edit form', function (): void {
    seedSsoConfiguration();

    $this->actingAs($this->admin)->get('/settings/sso/11111111-1111-1111-1111-111111111111/edit')->assertOk()->assertSee('Primary OIDC');
});

it('updates a configuration', function (): void {
    seedSsoConfiguration();

    $this->actingAs($this->admin)->put('/settings/sso/11111111-1111-1111-1111-111111111111', [
        'display_name' => 'Renamed',
        'enabled' => 1,
        'enforce' => 1,
        'jit_mode' => 'auto_create',
        'allowed_email_domains' => 'acme.com',
        'config' => ['client_id' => 'newcid'],
    ])->assertRedirect('/settings/sso');

    expect(SsoConfigurationModel::query()->find('11111111-1111-1111-1111-111111111111')?->display_name)->toBe('Renamed');
});

it('deletes a configuration', function (): void {
    seedSsoConfiguration();

    $this->actingAs($this->admin)->delete('/settings/sso/11111111-1111-1111-1111-111111111111')->assertRedirect('/settings/sso');

    expect(SsoConfigurationModel::query()->find('11111111-1111-1111-1111-111111111111'))->toBeNull();
});

it('runs the test action and reports the outcome', function (): void {
    seedSsoConfiguration();

    $this->actingAs($this->admin)
        ->post('/settings/sso/11111111-1111-1111-1111-111111111111/test')
        ->assertRedirect('/settings/sso');
});
