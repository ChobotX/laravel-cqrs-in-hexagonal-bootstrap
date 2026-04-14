<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Repository\UserTwoFactorStateRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Domain\User\Contract\ValueObject\UserTwoFactorState;
use App\Infrastructure\Eloquent\User\UserModel;
use Carbon\CarbonImmutable;
use DateTimeImmutable;

final readonly class EloquentUserTwoFactorStateRepository implements UserTwoFactorStateRepository
{
    public function get(UserId $userId): UserTwoFactorState
    {
        $model = UserModel::find($userId->value);

        if (! $model instanceof UserModel) {
            return new UserTwoFactorState(false, null, null, null);
        }

        return new UserTwoFactorState(
            emailEnabled: $model->email_two_factor_enabled,
            emailConfirmedAt: $model->email_two_factor_confirmed_at !== null ? DateTimeImmutable::createFromInterface($model->email_two_factor_confirmed_at) : null,
            totpSecret: is_string($model->totp_secret) ? $model->totp_secret : null,
            totpConfirmedAt: $model->totp_confirmed_at !== null ? DateTimeImmutable::createFromInterface($model->totp_confirmed_at) : null,
            totpRecoveryCodeHashes: $this->totpRecoveryCodeHashesFromModel($model),
        );
    }

    public function save(UserId $userId, UserTwoFactorState $userTwoFactorState): void
    {
        $model = UserModel::findOrFail($userId->value);
        $model->email_two_factor_enabled = $userTwoFactorState->emailEnabled;
        $model->email_two_factor_confirmed_at = $userTwoFactorState->emailConfirmedAt instanceof DateTimeImmutable
            ? CarbonImmutable::instance($userTwoFactorState->emailConfirmedAt)
            : null;
        $model->totp_secret = $userTwoFactorState->totpSecret;
        $model->totp_confirmed_at = $userTwoFactorState->totpConfirmedAt instanceof DateTimeImmutable
            ? CarbonImmutable::instance($userTwoFactorState->totpConfirmedAt)
            : null;
        $model->totp_recovery_code_hashes = $userTwoFactorState->totpRecoveryCodeHashes;
        $model->save();
    }

    /**
     * @return list<string>|null
     */
    private function totpRecoveryCodeHashesFromModel(UserModel $userModel): ?array
    {
        $raw = $userModel->getAttribute('totp_recovery_code_hashes');
        if (! is_array($raw)) {
            return null;
        }

        $hashes = [];
        foreach ($raw as $item) {
            if (is_string($item)) {
                $hashes[] = $item;
            }
        }

        return $hashes;
    }
}
