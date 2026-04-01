<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\RoleNotAssignedException;

it('has correct status code', function (): void {
    $exception = new RoleNotAssignedException('user-1', 'role-1');

    expect($exception->statusCode())->toBe(404);
});

it('has technical message with user and role', function (): void {
    $exception = new RoleNotAssignedException('user-1', 'role-1');

    expect($exception->getMessage())->toBe('Role [role-1] is not assigned to user [user-1].');
});

it('translates user message', function (): void {
    $exception = new RoleNotAssignedException('user-1', 'role-1');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.role_not_assigned');
});

it('exposes user id and role id', function (): void {
    $exception = new RoleNotAssignedException('user-1', 'role-1');

    expect($exception->userId)->toBe('user-1')
        ->and($exception->roleId)->toBe('role-1');
});
