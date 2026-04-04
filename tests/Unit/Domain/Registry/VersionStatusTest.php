<?php

declare(strict_types=1);

use App\Domain\Registry\Contract\Enum\VersionStatus;

it('has a Draft case with correct value', function (): void {
    expect(VersionStatus::Draft->value)->toBe('draft');
});

it('has an Active case with correct value', function (): void {
    expect(VersionStatus::Active->value)->toBe('active');
});

it('has a Deprecated case with correct value', function (): void {
    expect(VersionStatus::Deprecated->value)->toBe('deprecated');
});

it('has exactly 3 cases', function (): void {
    expect(VersionStatus::cases())->toHaveCount(3);
});
