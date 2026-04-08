<?php

declare(strict_types=1);

use App\Domain\AuditLog\Exception\InvalidAuditLogIdException;
use App\Domain\AuditLog\ValueObject\AuditLogId;

it('creates a valid audit log id', function (): void {
    $id = new AuditLogId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('compares equality', function (): void {
    $a = new AuditLogId('550e8400-e29b-41d4-a716-446655440000');
    $b = new AuditLogId('550e8400-e29b-41d4-a716-446655440000');
    $c = new AuditLogId('660e8400-e29b-41d4-a716-446655440000');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});

it('throws on invalid uuid', function (): void {
    new AuditLogId('not-a-uuid');
})->throws(InvalidAuditLogIdException::class);
