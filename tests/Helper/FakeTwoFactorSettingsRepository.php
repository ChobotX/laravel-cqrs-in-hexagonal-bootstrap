<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\User\Contract\Repository\TwoFactorSettingsRepository;
use App\Domain\User\Contract\ValueObject\TwoFactorSettings;

final class FakeTwoFactorSettingsRepository implements TwoFactorSettingsRepository
{
    public ?TwoFactorSettings $captured = null;

    public function __construct(
        private TwoFactorSettings $twoFactorSettings,
    ) {}

    public function get(): TwoFactorSettings
    {
        return $this->twoFactorSettings;
    }

    public function save(TwoFactorSettings $twoFactorSettings): void
    {
        $this->captured = $twoFactorSettings;
        $this->twoFactorSettings = $twoFactorSettings;
    }
}
