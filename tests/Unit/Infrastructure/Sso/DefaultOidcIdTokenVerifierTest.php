<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Infrastructure\Sso\DefaultOidcIdTokenVerifier;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;

it('rejects when JWKS endpoint is unreachable', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/jwks' => fn (): never => throw new ConnectionException('down')]);

    new DefaultOidcIdTokenVerifier($factory)('abc', 'https://idp.example.com/jwks');
})->throws(SsoLoginRejectedException::class, 'jwks_unreachable');

it('rejects when JWKS endpoint returns non-2xx', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/jwks' => HttpFactory::response([], 500)]);

    new DefaultOidcIdTokenVerifier($factory)('abc', 'https://idp.example.com/jwks');
})->throws(SsoLoginRejectedException::class, 'jwks_fetch_failed');

it('rejects when JWKS document is malformed', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/jwks' => HttpFactory::response([
        'keys' => [['kty' => 'RSA', 'kid' => 'k1', 'alg' => 'RS256', 'use' => 'sig', 'n' => 'bad', 'e' => 'AQAB']],
    ], 200)]);

    new DefaultOidcIdTokenVerifier($factory)('abc', 'https://idp.example.com/jwks');
})->throws(SsoLoginRejectedException::class);

it('rejects when JWKS is empty', function (): void {
    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/jwks' => HttpFactory::response(['keys' => []], 200)]);

    new DefaultOidcIdTokenVerifier($factory)('abc', 'https://idp.example.com/jwks');
})->throws(SsoLoginRejectedException::class);

it('decodes a valid RS256 token signed by a JWKS key', function (): void {
    $resource = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]);
    expect($resource)->not->toBeFalse();
    assert($resource !== false);

    openssl_pkey_export($resource, $privatePem);
    assert(is_string($privatePem));
    $details = openssl_pkey_get_details($resource);
    assert(is_array($details));

    /** @var array{n: string, e: string} $rsa */
    $rsa = $details['rsa'];
    $n = rtrim(strtr(base64_encode($rsa['n']), '+/', '-_'), '=');
    $e = rtrim(strtr(base64_encode($rsa['e']), '+/', '-_'), '=');

    $token = JWT::encode([
        'sub' => 'user-1', 'email' => 'u@example.com', 'aud' => 'cid', 'iss' => 'https://idp', 'exp' => time() + 60,
    ], $privatePem, 'RS256', 'k1');

    $factory = new HttpFactory;
    $factory->fake(['idp.example.com/jwks' => HttpFactory::response([
        'keys' => [['kty' => 'RSA', 'kid' => 'k1', 'alg' => 'RS256', 'use' => 'sig', 'n' => $n, 'e' => $e]],
    ], 200)]);

    $claims = new DefaultOidcIdTokenVerifier($factory)($token, 'https://idp.example.com/jwks');

    expect($claims['sub'])->toBe('user-1')->and($claims['aud'])->toBe('cid');
});
