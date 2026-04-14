<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\User\Contract\Repository\PasswordRotationSettingsRepository;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;

final class FakePasswordRotationSettingsRepository implements PasswordRotationSettingsRepository
{
    public ?PasswordRotationSettings $captured = null;

    public function __construct(
        public PasswordRotationSettings $settings,
    ) {}

    public function get(): PasswordRotationSettings
    {
        return $this->settings;
    }

    public function save(PasswordRotationSettings $passwordRotationSettings): void
    {
        $this->captured = $passwordRotationSettings;
        $this->settings = $passwordRotationSettings;
    }
}
