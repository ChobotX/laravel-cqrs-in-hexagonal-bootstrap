<?php

declare(strict_types=1);

use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use App\Infrastructure\Sso\Exception\SsoDiscoveryFailedException;
use App\Infrastructure\Sso\OidcDiscoveryClient;
use Illuminate\Http\Client\Factory as HttpFactory;

it('rejects non-HTTPS URLs', function (): void {
    new OidcDiscoveryClient(new HttpFactory)->fetch('http://idp.example.com/.well-known/openid-configuration');
})->throws(SsoConfigurationInvalidException::class);

it('returns an empty defaulted document when discovery responds with no endpoints', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/*' => HttpFactory::response([], 200),
    ]);

    $document = new OidcDiscoveryClient($factory)->fetch('https://idp.example.com/.well-known/openid-configuration');

    expect($document['authorization_endpoint'])->toBe('')
        ->and($document['token_endpoint'])->toBe('');
});

it('returns discovery document endpoints when present', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/*' => HttpFactory::response([
            'authorization_endpoint' => 'https://idp.example.com/authorize',
            'token_endpoint' => 'https://idp.example.com/token',
            'issuer' => 'https://idp.example.com',
        ], 200),
    ]);

    $document = new OidcDiscoveryClient($factory)->fetch('https://idp.example.com/.well-known/openid-configuration');

    expect($document['authorization_endpoint'])->toBe('https://idp.example.com/authorize')
        ->and($document['issuer'])->toBe('https://idp.example.com');
});

it('throws when the discovery endpoint returns non-2xx', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/*' => HttpFactory::response([], 500),
    ]);

    new OidcDiscoveryClient($factory)->fetch('https://idp.example.com/.well-known/openid-configuration');
})->throws(SsoDiscoveryFailedException::class);

it('throws when the discovery endpoint is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake([
        'idp.example.com/*' => fn (): never => throw new Illuminate\Http\Client\ConnectionException('down'),
    ]);

    new OidcDiscoveryClient($factory)->fetch('https://idp.example.com/.well-known/openid-configuration');
})->throws(SsoDiscoveryFailedException::class);
