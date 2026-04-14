<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\Contract\Exception\PasswordPreviouslyUsedException;
use App\Domain\User\Contract\Repository\PasswordHistoryRepository;
use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\Service\PasswordManager;
use App\Infrastructure\Eloquent\User\UserModel;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Illuminate\Support\Facades\Hash;

final readonly class EloquentPasswordManager implements PasswordManager
{
    public function __construct(
        private PasswordRotationSettingsRepository $passwordRotationSettingsRepository,
        private PasswordHistoryRepository $passwordHistoryRepository,
    ) {}

    public function setPassword(string $userId, string $rawPassword): void
    {
        $userModel = UserModel::find($userId);

        if (! $userModel instanceof UserModel) {
            return;
        }

        $passwordRotationSettings = $this->passwordRotationSettingsRepository->get();
        $historyLimit = $passwordRotationSettings->normalizedHistoryCount();

        $this->assertPasswordNotReused($userId, $userModel->password, $rawPassword, $historyLimit);

        $now = new DateTimeImmutable;
        $previousHash = $userModel->password;

        if (is_string($previousHash) && $previousHash !== '') {
            $this->passwordHistoryRepository->append($userId, $previousHash, $now);
            $this->passwordHistoryRepository->pruneToMaxEntries($userId, $historyLimit);
        }

        $userModel->password = Hash::make($rawPassword);
        $userModel->password_changed_at = CarbonImmutable::instance($now);
        $userModel->save();
    }

    private function assertPasswordNotReused(
        string $userId,
        mixed $currentHash,
        string $rawPassword,
        int $historyLimit,
    ): void {
        $candidates = [];

        if (is_string($currentHash) && $currentHash !== '') {
            $candidates[] = $currentHash;
        }

        foreach ($this->passwordHistoryRepository->listRecentHashes($userId, $historyLimit) as $hash) {
            $candidates[] = $hash;
        }

        foreach ($candidates as $candidate) {
            if (Hash::check($rawPassword, $candidate)) {
                throw new PasswordPreviouslyUsedException;
            }
        }
    }
}
