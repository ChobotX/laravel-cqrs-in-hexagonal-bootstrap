<?php

declare(strict_types=1);

use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use App\Infrastructure\Sso\SocialProviderCatalog;

it('returns endpoints for each supported provider', function (string $key): void {
    $endpoints = (new SocialProviderCatalog)->endpointsFor($key);

    expect($endpoints)->toHaveKeys(['authorize', 'token', 'userinfo', 'scope']);
})->with(['google', 'microsoft', 'github']);

it('rejects unknown providers', function (): void {
    (new SocialProviderCatalog)->endpointsFor('unknown');
})->throws(SsoConfigurationInvalidException::class);
