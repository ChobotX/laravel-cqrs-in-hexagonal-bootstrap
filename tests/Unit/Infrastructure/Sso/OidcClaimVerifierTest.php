<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use App\Infrastructure\Sso\OidcClaimVerifier;

it('accepts claims that pass every check', function (): void {
    new OidcClaimVerifier()->verify(
        claims: ['aud' => 'cid', 'iss' => 'https://idp', 'exp' => time() + 60, 'nonce' => 'n'],
        endpoints: ['issuer' => 'https://idp'],
        expectedClientId: 'cid',
        expectedNonce: 'n',
    );

    expect(true)->toBeTrue();
});

it('rejects when aud does not match client_id', function (): void {
    new OidcClaimVerifier()->verify(
        claims: ['aud' => 'other', 'iss' => 'https://idp'],
        endpoints: ['issuer' => 'https://idp'],
        expectedClientId: 'cid',
        expectedNonce: null,
    );
})->throws(SsoLoginRejectedException::class, 'aud_mismatch');

it('rejects when issuer is missing from discovery', function (): void {
    new OidcClaimVerifier()->verify(
        claims: ['aud' => 'cid', 'iss' => 'https://idp'],
        endpoints: ['issuer' => ''],
        expectedClientId: 'cid',
        expectedNonce: null,
    );
})->throws(SsoConfigurationInvalidException::class);

it('rejects when iss does not match expected issuer', function (): void {
    new OidcClaimVerifier()->verify(
        claims: ['aud' => 'cid', 'iss' => 'https://other'],
        endpoints: ['issuer' => 'https://idp'],
        expectedClientId: 'cid',
        expectedNonce: null,
    );
})->throws(SsoLoginRejectedException::class, 'iss_mismatch');

it('rejects when exp claim is in the past', function (): void {
    new OidcClaimVerifier()->verify(
        claims: ['aud' => 'cid', 'iss' => 'https://idp', 'exp' => time() - 10],
        endpoints: ['issuer' => 'https://idp'],
        expectedClientId: 'cid',
        expectedNonce: null,
    );
})->throws(SsoLoginRejectedException::class, 'id_token_expired');

it('rejects when nonce does not match', function (): void {
    new OidcClaimVerifier()->verify(
        claims: ['aud' => 'cid', 'iss' => 'https://idp', 'nonce' => 'wrong'],
        endpoints: ['issuer' => 'https://idp'],
        expectedClientId: 'cid',
        expectedNonce: 'expected',
    );
})->throws(SsoLoginRejectedException::class, 'nonce_mismatch');
