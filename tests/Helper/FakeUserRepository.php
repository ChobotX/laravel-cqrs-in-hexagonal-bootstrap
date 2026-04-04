<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;

final class FakeUserRepository implements UserRepository
{
    use PaginatesArray;
    use SortsArray;

    /** @var list<User> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param  array<string, User>  $users */
    public function __construct(
        private array $users = [],
    ) {}

    /** @return list<User> */
    public function all(?array $onlyIds = null, array $sortings = []): array
    {
        $users = array_values($this->users);

        if ($onlyIds !== null) {
            $users = array_values(array_filter(
                $users,
                fn (User $user): bool => in_array($user->id->value, $onlyIds, true),
            ));
        }

        return $this->sortArray($users, $sortings, $this->sortValueExtractor(...));
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

    /** @return PaginatedResult<User> */
    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = []): PaginatedResult
    {
        return $this->paginateArray($this->all($onlyIds, $sortings), $pagination);
    }

    public function count(): int
    {
        return count($this->users);
    }

    /** @return list<User> */
    public function search(string $term, array $excludeUserIds, int $limit, ?array $onlyIds = null): array
    {
        $normalizedTerm = $this->stripDiacritics(mb_strtolower($term));

        $results = array_filter(
            $this->users,
            function (User $user) use ($normalizedTerm, $excludeUserIds, $onlyIds): bool {
                if ($onlyIds !== null && ! in_array($user->id->value, $onlyIds, true)) {
                    return false;
                }

                if (in_array($user->id->value, $excludeUserIds, true)) {
                    return false;
                }

                return str_contains($this->stripDiacritics(mb_strtolower($user->name->value)), $normalizedTerm)
                    || str_contains($this->stripDiacritics(mb_strtolower($user->email->value)), $normalizedTerm);
            },
        );

        return array_values(array_slice($results, 0, $limit));
    }

    /** @return callable(User): (string|int) */
    private function sortValueExtractor(string $column): callable
    {
        return match ($column) {
            'name' => fn (User $user): string => $user->name->value,
            'email' => fn (User $user): string => $user->email->value,
            default => fn (User $user): int => 0,
        };
    }

    private function stripDiacritics(string $value): string
    {
        $transliterated = transliterator_transliterate('Any-Latin; Latin-ASCII', $value);

        return $transliterated === false ? $value : $transliterated;
    }
}
