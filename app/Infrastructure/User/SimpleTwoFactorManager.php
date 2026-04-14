<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Service\TwoFactorManager;
use Illuminate\Support\Facades\Hash;

final readonly class SimpleTwoFactorManager implements TwoFactorManager
{
    private const string TOTP_BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /** Decoded TOTP key size in bytes (160-bit secret → 32 Base32 chars in otpauth URIs). */
    private const int TOTP_SECRET_BYTES = 20;

    private const int BASE32_BITS_PER_CHAR = 5;

    private const int BASE32_VALUE_MASK = 31;

    private const int BITS_PER_BYTE = 8;

    private const int TOTP_CODE_LENGTH = 6;

    private const int TOTP_WINDOW = 1;

    private const int TOTP_PERIOD_SECONDS = 30;

    private const int EMAIL_CODE_MIN = 100000;

    private const int EMAIL_CODE_MAX = 999999;

    private const int HMAC_OFFSET_INDEX = 19;

    private const int HMAC_NIBBLE_MASK = 0x0F;

    private const int HMAC_HIGH_BIT_MASK = 0x7F;

    private const int HMAC_BYTE_MASK = 0xFF;

    private const int SHIFT_24 = 24;

    private const int SHIFT_16 = 16;

    private const int SHIFT_8 = 8;

    private const int OFFSET_SECOND_BYTE = 2;

    private const int OFFSET_THIRD_BYTE = 3;

    private const int TOTP_RECOVERY_CODE_COUNT = 10;

    private const int TOTP_RECOVERY_HEX_BYTES = 8;

    private const int TOTP_RECOVERY_SEGMENT_LENGTH = 4;

    private const int TOTP_RECOVERY_SEGMENT_OFFSET_MULTIPLIER_2 = 2;

    private const int TOTP_RECOVERY_SEGMENT_OFFSET_MULTIPLIER_3 = 3;

    public function generateTotpSecret(): string
    {
        return $this->encodeBase32(random_bytes(self::TOTP_SECRET_BYTES));
    }

    public function buildTotpUri(string $issuer, string $accountName, string $secret): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            rawurlencode($issuer),
            rawurlencode($accountName),
            rawurlencode($secret),
            rawurlencode($issuer),
        );
    }

    public function verifyTotpCode(string $secret, string $code): bool
    {
        if (preg_match('/^\d{6}$/', $code) !== 1) {
            return false;
        }

        $key = $this->decodeBase32($secret);
        if ($key === false) {
            return false;
        }

        $counter = (int) floor(time() / self::TOTP_PERIOD_SECONDS);

        for ($offset = -self::TOTP_WINDOW; $offset <= self::TOTP_WINDOW; $offset++) {
            if (hash_equals($this->totpAt($key, $counter + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    public function generateEmailCode(): string
    {
        return (string) random_int(self::EMAIL_CODE_MIN, self::EMAIL_CODE_MAX);
    }

    public function hashChallengeCode(string $code): string
    {
        return Hash::make($code);
    }

    public function verifyChallengeCode(string $plainCode, string $hash): bool
    {
        return Hash::check($plainCode, $hash);
    }

    public function generateTotpRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < self::TOTP_RECOVERY_CODE_COUNT; $i++) {
            $codes[] = $this->randomTotpRecoveryCode();
        }

        return $codes;
    }

    public function hashTotpRecoveryCode(string $plain): string
    {
        return Hash::make($plain);
    }

    public function verifyTotpRecoveryCode(string $plain, string $hash): bool
    {
        return Hash::check($plain, $hash);
    }

    private function randomTotpRecoveryCode(): string
    {
        $raw = strtoupper(bin2hex(random_bytes(self::TOTP_RECOVERY_HEX_BYTES)));
        $segment = self::TOTP_RECOVERY_SEGMENT_LENGTH;

        return substr($raw, 0, $segment)
            .'-'.substr($raw, $segment, $segment)
            .'-'.substr($raw, $segment * self::TOTP_RECOVERY_SEGMENT_OFFSET_MULTIPLIER_2, $segment)
            .'-'.substr($raw, $segment * self::TOTP_RECOVERY_SEGMENT_OFFSET_MULTIPLIER_3, $segment);
    }

    private function totpAt(string $binaryKey, int $counter): string
    {
        $counterBytes = pack('N*', 0).pack('N*', $counter);
        $hash = hash_hmac('sha1', $counterBytes, $binaryKey, true);
        $offset = ord($hash[self::HMAC_OFFSET_INDEX]) & self::HMAC_NIBBLE_MASK;
        $binary = ((ord($hash[$offset]) & self::HMAC_HIGH_BIT_MASK) << self::SHIFT_24)
            | ((ord($hash[$offset + 1]) & self::HMAC_BYTE_MASK) << self::SHIFT_16)
            | ((ord($hash[$offset + self::OFFSET_SECOND_BYTE]) & self::HMAC_BYTE_MASK) << self::SHIFT_8)
            | (ord($hash[$offset + self::OFFSET_THIRD_BYTE]) & self::HMAC_BYTE_MASK);

        return str_pad((string) ($binary % (self::EMAIL_CODE_MAX + 1)), self::TOTP_CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    private function encodeBase32(string $binary): string
    {
        $len = strlen($binary);
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $buffer = ($buffer << self::BITS_PER_BYTE) | ord($binary[$i]);
            $bitsLeft += self::BITS_PER_BYTE;
            while ($bitsLeft >= self::BASE32_BITS_PER_CHAR) {
                $bitsLeft -= self::BASE32_BITS_PER_CHAR;
                $result .= self::TOTP_BASE32_ALPHABET[($buffer >> $bitsLeft) & self::BASE32_VALUE_MASK];
            }
        }

        if ($bitsLeft > 0) {
            $pad = self::BASE32_BITS_PER_CHAR - $bitsLeft;
            $result .= self::TOTP_BASE32_ALPHABET[($buffer << $pad) & self::BASE32_VALUE_MASK];
        }

        return $result;
    }

    /**
     * @return false|string uppercase Base32 body without padding, or false when empty or invalid characters
     */
    private function normalizedBase32BodyOrFalse(string $encoded): string|false
    {
        $normalized = strtoupper(rtrim($encoded, '='));
        if ($normalized === '') {
            return false;
        }

        if (preg_match('/[^A-Z2-7]/', $normalized) === 1) {
            return false;
        }

        return $normalized;
    }

    /**
     * @return false|string raw key bytes for HMAC-SHA1 (RFC 4648 Base32, case-insensitive, padding optional)
     */
    private function decodeBase32(string $encoded): string|false
    {
        $encoded = $this->normalizedBase32BodyOrFalse($encoded);
        if ($encoded === false) {
            return false;
        }

        $map = array_flip(str_split(self::TOTP_BASE32_ALPHABET));
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';
        $len = strlen($encoded);
        for ($i = 0; $i < $len; $i++) {
            $char = $encoded[$i];
            $buffer = ($buffer << self::BASE32_BITS_PER_CHAR) | $map[$char];
            $bitsLeft += self::BASE32_BITS_PER_CHAR;
            while ($bitsLeft >= self::BITS_PER_BYTE) {
                $bitsLeft -= self::BITS_PER_BYTE;
                $result .= chr(($buffer >> $bitsLeft) & self::HMAC_BYTE_MASK);
            }
        }

        if ($bitsLeft > 0) {
            $paddingBits = ($buffer << (self::BITS_PER_BYTE - $bitsLeft)) & self::HMAC_BYTE_MASK;
            if ($paddingBits !== 0) {
                return false;
            }
        }

        return $result;
    }
}
