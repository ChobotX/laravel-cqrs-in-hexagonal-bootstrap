<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;
use App\Domain\User\Contract\Enum\TwoFactorSettingsAction;

/**
 * Orchestrates a user's own two-factor setting changes via one controller dispatch.
 * Handler fans out to enable/disable/confirm commands for email OTP and TOTP.
 *
 * Which optional fields are required depends on `action`:
 *   - EmailSave    — `emailEnabled`
 *   - TotpSave     — `totpEnabled`
 *   - TotpConfirm  — `totpCode`
 *   - TotpDisable  — none
 */
#[SkipPermissionCheck('Authenticated users manage their own two-factor settings')]
final readonly class ManageOwnTwoFactorSettingsCommand implements Command
{
    public function __construct(
        /** Self-edit target; always the authenticated user. */
        public string $userId,
        public TwoFactorSettingsAction $action,
        /** Email OTP toggle for EmailSave. */
        public ?bool $emailEnabled = null,
        /** TOTP toggle for TotpSave. */
        public ?bool $totpEnabled = null,
        /** Six-digit code for TotpConfirm. */
        public ?string $totpCode = null,
    ) {}
}
