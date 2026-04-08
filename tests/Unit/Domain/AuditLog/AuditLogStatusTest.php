<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;

it('has success and failure cases', function (): void {
    expect(AuditLogStatus::Success->value)->toBe('success')
        ->and(AuditLogStatus::Failure->value)->toBe('failure');
});

it('creates from string value', function (): void {
    expect(AuditLogStatus::from('success'))->toBe(AuditLogStatus::Success)
        ->and(AuditLogStatus::from('failure'))->toBe(AuditLogStatus::Failure);
});
