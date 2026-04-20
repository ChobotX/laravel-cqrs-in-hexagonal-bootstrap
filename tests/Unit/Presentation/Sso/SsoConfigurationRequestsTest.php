<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Sso\StoreSsoConfigurationRequest;
use App\Presentation\Http\Request\Sso\UpdateSsoConfigurationRequest;

it('parses store request fields', function (): void {
    $storeSsoConfigurationRequest = StoreSsoConfigurationRequest::create('/settings/sso', 'POST', [
        'provider_type' => 'oidc',
        'slug' => 'okta',
        'display_name' => 'Okta',
        'enabled' => '1',
        'enforce' => '0',
        'jit_mode' => 'invited_only',
        'allowed_email_domains' => 'acme.com, , partner.com',
        'config' => ['client_id' => 'cid', 'extra' => null, 'opts' => ['a' => 1]],
    ]);

    expect($storeSsoConfigurationRequest->providerType())->toBe('oidc')
        ->and($storeSsoConfigurationRequest->slug())->toBe('okta')
        ->and($storeSsoConfigurationRequest->displayName())->toBe('Okta')
        ->and($storeSsoConfigurationRequest->enabled())->toBeTrue()
        ->and($storeSsoConfigurationRequest->enforce())->toBeFalse()
        ->and($storeSsoConfigurationRequest->jitMode())->toBe('invited_only')
        ->and($storeSsoConfigurationRequest->allowedEmailDomains())->toBe(['acme.com', 'partner.com'])
        ->and($storeSsoConfigurationRequest->configMap())->toBe(['client_id' => 'cid', 'extra' => null, 'opts' => ['a' => 1]]);
});

it('returns empty allowed domains when input is missing or non-string', function (): void {
    $storeSsoConfigurationRequest = StoreSsoConfigurationRequest::create('/settings/sso', 'POST', []);

    expect($storeSsoConfigurationRequest->allowedEmailDomains())->toBe([])
        ->and($storeSsoConfigurationRequest->configMap())->toBe([])
        ->and($storeSsoConfigurationRequest->providerType())->toBe('');
});

it('parses update request fields', function (): void {
    $updateSsoConfigurationRequest = UpdateSsoConfigurationRequest::create('/settings/sso/1', 'PUT', [
        'display_name' => 'X',
        'enabled' => '1',
        'enforce' => '1',
        'jit_mode' => 'auto_create',
        'allowed_email_domains' => 'acme.com',
        'config' => ['client_id' => 'cid'],
    ]);

    expect($updateSsoConfigurationRequest->displayName())->toBe('X')
        ->and($updateSsoConfigurationRequest->enabled())->toBeTrue()
        ->and($updateSsoConfigurationRequest->enforce())->toBeTrue()
        ->and($updateSsoConfigurationRequest->jitMode())->toBe('auto_create')
        ->and($updateSsoConfigurationRequest->allowedEmailDomains())->toBe(['acme.com'])
        ->and($updateSsoConfigurationRequest->configMap())->toBe(['client_id' => 'cid']);
});

it('returns empty allowed domains and config in update request when missing', function (): void {
    $updateSsoConfigurationRequest = UpdateSsoConfigurationRequest::create('/settings/sso/1', 'PUT', []);

    expect($updateSsoConfigurationRequest->allowedEmailDomains())->toBe([])
        ->and($updateSsoConfigurationRequest->configMap())->toBe([])
        ->and($updateSsoConfigurationRequest->displayName())->toBe('');
});
