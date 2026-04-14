<?php

declare(strict_types=1);

use App\Domain\User\Contract\Command\AdminResetUserTwoFactorCommand;
use App\Domain\User\Contract\Command\ConfirmTotpSetupCommand;
use App\Domain\User\Contract\Command\DisableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\DisableTotpTwoFactorCommand;
use App\Domain\User\Contract\Command\EnableEmailTwoFactorCommand;
use App\Domain\User\Contract\Command\IssueEmailTwoFactorChallengeCommand;
use App\Domain\User\Contract\Command\MarkTotpBackupCodesDownloadedCommand;
use App\Domain\User\Contract\Command\StartTotpSetupCommand;
use App\Domain\User\Contract\Command\VerifyTwoFactorChallengeCommand;
use App\Domain\User\Contract\Query\GetTotpSetupQuery;
use App\Domain\User\Contract\ValueObject\EmailTwoFactorChallenge;
use App\Domain\User\Contract\ValueObject\TotpSetup;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;
use App\Domain\User\Exception\InvalidTwoFactorCodeException;
use App\Domain\User\Exception\InvalidTwoFactorPolicyException;
use App\Domain\User\Exception\TotpBackupCodesDownloadRequiredException;

it('covers two-factor command/query contracts and value objects', function (): void {
    $confirm = new ConfirmTotpSetupCommand('u1', '123456');
    $enable = new EnableEmailTwoFactorCommand('u1');
    $disableEmail = new DisableEmailTwoFactorCommand('u1');
    $disableTotp = new DisableTotpTwoFactorCommand('u1');
    $issue = new IssueEmailTwoFactorChallengeCommand('u1');
    $start = new StartTotpSetupCommand('u1');
    $verify = new VerifyTwoFactorChallengeCommand('u1', 'totp', '123456');
    $query = new GetTotpSetupQuery('u1');
    $markBackupDownloaded = new MarkTotpBackupCodesDownloadedCommand('u1');
    $adminResetTwoFactor = new AdminResetUserTwoFactorCommand('u1');
    $setup = new TotpSetup('secret', 'otpauth://test', true, null, true);
    $challenge = new EmailTwoFactorChallenge('hash', new DateTimeImmutable('+1 minute'), 0, false);

    expect($confirm->code)->toBe('123456')
        ->and($enable->userId)->toBe('u1')
        ->and($disableEmail->userId)->toBe('u1')
        ->and($disableTotp->userId)->toBe('u1')
        ->and($issue->userId)->toBe('u1')
        ->and($start->userId)->toBe('u1')
        ->and($verify->method)->toBe('totp')
        ->and($query->userId)->toBe('u1')
        ->and($markBackupDownloaded->userId)->toBe('u1')
        ->and($adminResetTwoFactor->targetUserId)->toBe('u1')
        ->and($setup->confirmed)->toBeTrue()
        ->and($challenge->isExpired(new DateTimeImmutable('+2 minutes')))->toBeTrue();
});

it('returns correct status and message for totp backup codes download required exception', function (): void {
    $translator = new class implements App\Contract\Translation\Translator
    {
        public function translate(string $key, array $replace = [], ?string $locale = null): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    $exception = new TotpBackupCodesDownloadRequiredException;

    expect($exception->statusCode())->toBe(422)
        ->and($exception->userMessage($translator))->toBe('messages.settings.totp_backup_codes_download_required');
});

it('returns correct status and message for invalid two-factor code exception', function (): void {
    $translator = new class implements App\Contract\Translation\Translator
    {
        public function translate(string $key, array $replace = [], ?string $locale = null): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    $exception = new InvalidTwoFactorCodeException;

    expect($exception->statusCode())->toBe(422)
        ->and($exception->userMessage($translator))->toBe('messages.auth.invalid_two_factor_code');
});

it('covers two-factor settings false branch and policy exception', function (): void {
    $translator = new class implements App\Contract\Translation\Translator
    {
        public function translate(string $key, array $replace = [], ?string $locale = null): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    $settings = new TwoFactorSettings(requiredForAllUsers: false, emailOtpEnabled: false, totpEnabled: false);
    $exception = new InvalidTwoFactorPolicyException('messages.exceptions.invalid_two_factor_policy_requires_method');

    expect($settings->hasAnyMethodEnabled())->toBeFalse()
        ->and($exception->statusCode())->toBe(422)
        ->and($exception->userMessage($translator))->toBe('messages.exceptions.invalid_two_factor_policy_requires_method');
});
