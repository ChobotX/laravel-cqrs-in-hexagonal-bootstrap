<?php

declare(strict_types=1);

use App\Infrastructure\User\SimpleTwoFactorManager;

it('generates and verifies two-factor artifacts', function (): void {
    $manager = new SimpleTwoFactorManager;
    $secret = $manager->generateTotpSecret();
    $uri = $manager->buildTotpUri('Issuer', 'user@example.com', $secret);
    $emailCode = $manager->generateEmailCode();
    $hash = $manager->hashChallengeCode($emailCode);

    $ref = new ReflectionClass($manager);
    $decodeBase32 = $ref->getMethod('decodeBase32');

    $binaryKey = $decodeBase32->invoke($manager, $secret);
    expect($binaryKey)->not->toBeFalse();
    assert(is_string($binaryKey));
    expect(strlen($binaryKey))->toBe(20);

    $reflectionMethod = $ref->getMethod('totpAt');

    $validCode = $reflectionMethod->invoke($manager, $binaryKey, (int) floor(time() / 30));

    $recoveryCodes = $manager->generateTotpRecoveryCodes();
    $recoveryHash = $manager->hashTotpRecoveryCode($recoveryCodes[0]);

    expect($secret)->toBeString()
        ->and($secret)->toMatch('/^[A-Z2-7]+$/')
        ->and(strlen($secret))->toBe(32)
        ->and($uri)->toContain('otpauth://totp/')
        ->and($manager->verifyTotpCode('INVALID9CHARS', '123456'))->toBeFalse()
        ->and($manager->verifyTotpCode($secret, 'abc123'))->toBeFalse()
        ->and($manager->verifyTotpCode($secret, '111111'))->toBeFalse()
        ->and($manager->verifyTotpCode($secret, $validCode))->toBeTrue()
        ->and($manager->verifyChallengeCode($emailCode, $hash))->toBeTrue()
        ->and($recoveryCodes)->toHaveCount(10)
        ->and($manager->verifyTotpRecoveryCode($recoveryCodes[0], $recoveryHash))->toBeTrue()
        ->and($manager->verifyTotpRecoveryCode('WRONG-CODE-HERE-NOW', $recoveryHash))->toBeFalse();
});

it('rejects totp secrets that are not valid base32', function (): void {
    $manager = new SimpleTwoFactorManager;

    expect($manager->verifyTotpCode('', '123456'))->toBeFalse()
        ->and($manager->verifyTotpCode('====', '123456'))->toBeFalse()
        ->and($manager->verifyTotpCode('0', '123456'))->toBeFalse()
        ->and($manager->verifyTotpCode('7', '123456'))->toBeFalse();
});

it('round-trips base32 for byte lengths that need a final partial group', function (): void {
    $manager = new SimpleTwoFactorManager;
    $ref = new ReflectionClass($manager);
    $reflectionMethod = $ref->getMethod('encodeBase32');

    $decode = $ref->getMethod('decodeBase32');

    foreach (["\x00", 'x', 'ab', 'abc'] as $binary) {
        $encoded = $reflectionMethod->invoke($manager, $binary);
        expect($decode->invoke($manager, $encoded))->toBe($binary);
    }
});
