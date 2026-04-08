<?php

declare(strict_types=1);

use App\Domain\AuditLog\Service\CommandPayloadExtractor;

it('derives action label stripping Command suffix', function (): void {
    $extractor = new CommandPayloadExtractor;

    $command = new App\Domain\User\Contract\Command\CreateUserCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Test',
        email: 'test@example.com',
    );

    expect($extractor->deriveActionLabel($command))->toBe('Create User');
});

it('handles anonymous class names', function (): void {
    $extractor = new CommandPayloadExtractor;

    $fakeCommand = new class {};

    $label = $extractor->deriveActionLabel($fakeCommand);

    expect($label)->toBeString();
});
