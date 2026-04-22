<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Enum;

/**
 * Discriminator for {@see \App\Domain\User\Contract\Command\ManageOwnTwoFactorSettingsCommand}.
 */
enum TwoFactorSettingsAction: string
{
    case EmailSave = 'email-save';
    case TotpSave = 'totp-save';
    case TotpConfirm = 'totp-confirm';
    case TotpDisable = 'totp-disable';
}
