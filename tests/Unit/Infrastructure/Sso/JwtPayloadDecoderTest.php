<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Infrastructure\Sso\JwtPayloadDecoder;

/** @param array<string, scalar> $claims */
function encodeJwt(array $claims): string
{
    $header = rtrim(strtr(base64_encode('{"alg":"RS256","typ":"JWT"}'), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode((string) json_encode($claims)), '+/', '-_'), '=');

    return $header.'.'.$payload.'.signature';
}

it('decodes a well-formed JWT payload', function (): void {
    $decoder = new JwtPayloadDecoder;

    $claims = $decoder->decode(encodeJwt(['sub' => 'abc', 'email' => 'user@example.com', 'exp' => 2000000000]));

    expect($claims['sub'])->toBe('abc')
        ->and($claims['email'])->toBe('user@example.com');
});

it('rejects a token with the wrong number of segments', function (): void {
    (new JwtPayloadDecoder)->decode('invalid-token');
})->throws(SsoLoginRejectedException::class, 'malformed_id_token');

it('rejects a token with a non-JSON payload', function (): void {
    $header = rtrim(strtr(base64_encode('{}'), '+/', '-_'), '=');
    $payload = rtrim(strtr(base64_encode('not-json'), '+/', '-_'), '=');

    (new JwtPayloadDecoder)->decode($header.'.'.$payload.'.sig');
})->throws(SsoLoginRejectedException::class, 'malformed_id_token');

it('rejects a token whose payload is not base64url', function (): void {
    (new JwtPayloadDecoder)->decode('aaa.%%%.bbb');
})->throws(SsoLoginRejectedException::class, 'malformed_id_token');
