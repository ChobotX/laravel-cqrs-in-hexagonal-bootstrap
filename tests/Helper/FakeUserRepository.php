<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

final class FakeUserRepository implements UserRepository
{
    /** @var list<User> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param  array<string, User>  $users */
    public function __construct(
        private array $users = [],
    ) {}

    /** @return list<User> */
    public function all(): array
    {
        return array_values($this->users);
    }

    public function findById(UserId $userId): ?User
    {
        return $this->users[$userId->value] ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->email->value === $email) {
                return $user;
            }
        }

        return null;
    }

    public function create(User $user): void
    {
        $this->saved[] = $user;
    }

    public function update(User $user): void
    {
        $this->saved[] = $user;
    }

    public function delete(UserId $userId): void
    {
        $this->deleted[] = $userId->value;
    }

    /** @return list<User> */
    public function search(string $term, array $excludeUserIds, int $limit): array
    {
        $normalizedTerm = $this->stripDiacritics(mb_strtolower($term));

        $results = array_filter(
            $this->users,
            function (User $user) use ($normalizedTerm, $excludeUserIds): bool {
                if (in_array($user->id->value, $excludeUserIds, true)) {
                    return false;
                }

                return str_contains($this->stripDiacritics(mb_strtolower($user->name)), $normalizedTerm)
                    || str_contains($this->stripDiacritics(mb_strtolower($user->email->value)), $normalizedTerm);
            },
        );

        return array_values(array_slice($results, 0, $limit));
    }

    private function stripDiacritics(string $value): string
    {
        $transliterated = transliterator_transliterate('Any-Latin; Latin-ASCII', $value);

        return $transliterated === false ? $value : $transliterated;
    }
}
