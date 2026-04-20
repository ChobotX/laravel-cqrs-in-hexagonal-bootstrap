<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Sso\StoreSsoConfigurationRequest;
use App\Presentation\Http\Request\Sso\UpdateSsoConfigurationRequest;

it('parses store request fields', function (): void {
    $request = StoreSsoConfigurationRequest::create('/settings/sso', 'POST', [
        'provider_type' => 'oidc',
        'slug' => 'okta',
        'display_name' => 'Okta',
        'enabled' => '1',
        'enforce' => '0',
        'jit_mode' => 'invited_only',
        'allowed_email_domains' => 'acme.com, , partner.com',
        'config' => ['client_id' => 'cid', 'extra' => null, 'opts' => ['a' => 1]],
    ]);

    expect($request->providerType())->toBe('oidc')
        ->and($request->slug())->toBe('okta')
        ->and($request->displayName())->toBe('Okta')
        ->and($request->enabled())->toBeTrue()
        ->and($request->enforce())->toBeFalse()
        ->and($request->jitMode())->toBe('invited_only')
        ->and($request->allowedEmailDomains())->toBe(['acme.com', 'partner.com'])
        ->and($request->configMap())->toBe(['client_id' => 'cid', 'extra' => null, 'opts' => ['a' => 1]]);
});

it('returns empty allowed domains when input is missing or non-string', function (): void {
    $request = StoreSsoConfigurationRequest::create('/settings/sso', 'POST', []);

    expect($request->allowedEmailDomains())->toBe([])
        ->and($request->configMap())->toBe([])
        ->and($request->providerType())->toBe('');
});

it('parses update request fields', function (): void {
    $request = UpdateSsoConfigurationRequest::create('/settings/sso/1', 'PUT', [
        'display_name' => 'X',
        'enabled' => '1',
        'enforce' => '1',
        'jit_mode' => 'auto_create',
        'allowed_email_domains' => 'acme.com',
        'config' => ['client_id' => 'cid'],
    ]);

    expect($request->displayName())->toBe('X')
        ->and($request->enabled())->toBeTrue()
        ->and($request->enforce())->toBeTrue()
        ->and($request->jitMode())->toBe('auto_create')
        ->and($request->allowedEmailDomains())->toBe(['acme.com'])
        ->and($request->configMap())->toBe(['client_id' => 'cid']);
});

it('returns empty allowed domains and config in update request when missing', function (): void {
    $request = UpdateSsoConfigurationRequest::create('/settings/sso/1', 'PUT', []);

    expect($request->allowedEmailDomains())->toBe([])
        ->and($request->configMap())->toBe([])
        ->and($request->displayName())->toBe('');
});
