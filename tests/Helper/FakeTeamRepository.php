<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamRepository;
use App\Domain\Team\TeamSlug;

final class FakeTeamRepository implements TeamRepository
{
    use PaginatesArray;
    use SortsArray;

    /** @var list<Team> */
    public array $saved = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, Team> $teams */
    public function __construct(
        private array $teams = [],
    ) {}

    /** @return list<Team> */
    public function findAll(?array $onlyIds = null, array $sortings = []): array
    {
        $teams = array_values($this->teams);

        if ($onlyIds !== null) {
            $teams = array_values(array_filter(
                $teams,
                fn (Team $team): bool => in_array($team->id->value, $onlyIds, true),
            ));
        }

        return $this->sortArray($teams, $sortings, $this->sortValueExtractor(...));
    }

    public function findById(TeamId $teamId): ?Team
    {
        return $this->teams[$teamId->value] ?? null;
    }

    public function findBySlug(TeamSlug $teamSlug): ?Team
    {
        foreach ($this->teams as $team) {
            if ($team->slug->value === $teamSlug->value) {
                return $team;
            }
        }

        return null;
    }

    public function create(Team $team): void
    {
        $this->saved[] = $team;
        $this->teams[$team->id->value] = $team;
    }

    public function update(Team $team): void
    {
        $this->saved[] = $team;
        $this->teams[$team->id->value] = $team;
    }

    /** @return list<Team> */
    public function search(string $term, array $excludeTeamIds, int $limit): array
    {
        $normalizedTerm = mb_strtolower($term);

        $results = array_filter(
            $this->teams,
            fn (Team $team): bool => ! in_array($team->id->value, $excludeTeamIds, true)
                && ($normalizedTerm === '' || str_contains(mb_strtolower($team->name->value), $normalizedTerm)),
        );

        return array_values(array_slice($results, 0, $limit));
    }

    /** @return PaginatedResult<Team> */
    public function findAllPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = []): PaginatedResult
    {
        return $this->paginateArray($this->findAll($onlyIds, $sortings), $pagination);
    }

    public function count(): int
    {
        return count($this->teams);
    }

    public function delete(TeamId $teamId): void
    {
        $this->deleted[] = $teamId->value;
        unset($this->teams[$teamId->value]);
    }

    /** @return callable(Team): (string|int) */
    private function sortValueExtractor(string $column): callable
    {
        return match ($column) {
            'name' => fn (Team $t): string => $t->name->value,
            default => fn (Team $t): int => 0,
        };
    }
}
