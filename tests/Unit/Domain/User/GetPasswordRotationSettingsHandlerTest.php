<?php

declare(strict_types=1);

use App\Domain\User\Contract\Query\GetPasswordRotationSettingsQuery;
use App\Domain\User\Contract\ValueObject\PasswordRotationSettings;
use App\Domain\User\Handler\Query\GetPasswordRotationSettingsHandler;
use Tests\Helper\FakePasswordRotationSettingsRepository;

it('returns settings from the repository', function (): void {
    $expected = new PasswordRotationSettings(
        rotationEnabled: true,
        maxAgeDays: 60,
        historyCount: 12,
    );

    $handler = new GetPasswordRotationSettingsHandler(
        new FakePasswordRotationSettingsRepository($expected),
    );

    expect($handler->handle(new GetPasswordRotationSettingsQuery))->toEqual($expected);
});
