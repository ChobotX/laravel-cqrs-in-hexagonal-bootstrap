<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /** @return list<User> */
    public function all(): array;

    public function findById(UserId $userId): ?User;

    public function findByEmail(string $email): ?User;

    public function create(User $user): void;

    public function update(User $user): void;

    public function delete(UserId $userId): void;
}
