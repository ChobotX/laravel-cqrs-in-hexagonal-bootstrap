<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Service;

interface TwoFactorManager
{
    public function generateTotpSecret(): string;

    public function buildTotpUri(string $issuer, string $accountName, string $secret): string;

    public function verifyTotpCode(string $secret, string $code): bool;

    public function generateEmailCode(): string;

    public function hashChallengeCode(string $code): string;

    public function verifyChallengeCode(string $plainCode, string $hash): bool;

    /**
     * @return list<string>
     */
    public function generateTotpRecoveryCodes(): array;

    public function hashTotpRecoveryCode(string $plain): string;

    public function verifyTotpRecoveryCode(string $plain, string $hash): bool;
}
