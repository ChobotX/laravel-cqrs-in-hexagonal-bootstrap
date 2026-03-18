<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\DuplicateRoleAssignmentException;

it('has correct status code', function (): void {
    $exception = new DuplicateRoleAssignmentException('user-1', 'role-1');

    expect($exception->statusCode())->toBe(409);
});

it('has technical message with user and role', function (): void {
    $exception = new DuplicateRoleAssignmentException('user-1', 'role-1');

    expect($exception->getMessage())->toBe('Role [role-1] is already assigned to user [user-1].');
});

it('translates user message', function (): void {
    $exception = new DuplicateRoleAssignmentException('user-1', 'role-1');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.duplicate_role_assignment');
});

it('exposes user id and role id', function (): void {
    $exception = new DuplicateRoleAssignmentException('user-1', 'role-1');

    expect($exception->userId)->toBe('user-1')
        ->and($exception->roleId)->toBe('role-1');
});
