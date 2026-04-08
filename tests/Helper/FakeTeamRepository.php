<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Repository\TeamRepository;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;

final class FakeTeamRepository implements TeamRepository
{
    use FiltersArray;
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
    public function findAll(?array $onlyIds = null, array $sortings = [], array $filters = []): array
    {
        $teams = array_values($this->teams);

        if ($onlyIds !== null) {
            $teams = array_values(array_filter(
                $teams,
                fn (Team $team): bool => in_array($team->id->value, $onlyIds, true),
            ));
        }

        $teams = $this->filterArray($teams, $filters, $this->filterValueExtractor(...), $this->searchMatcher(...));

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
    public function findAllPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = [], array $filters = []): PaginatedResult
    {
        return $this->paginateArray($this->findAll($onlyIds, $sortings, $filters), $pagination);
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

    /** @return list<string> */
    public function ancestorTeamIds(TeamId $teamId): array
    {
        $ancestors = [];
        $current = $this->teams[$teamId->value] ?? null;

        while ($current?->parentTeamId !== null) {
            $parentId = $current->parentTeamId->value;
            if (in_array($parentId, $ancestors, true)) {
                break;
            }

            $ancestors[] = $parentId;
            $current = $this->teams[$parentId] ?? null;
        }

        return $ancestors;
    }

    /** @return callable(Team): (string|int) */
    private function sortValueExtractor(string $column): callable
    {
        return match ($column) {
            'name' => fn (Team $team): string => $team->name->value,
            'slug' => fn (Team $team): string => $team->slug->value,
            default => fn (Team $team): int => 0,
        };
    }

    private function filterValueExtractor(Team $team, string $column): string
    {
        return match ($column) {
            'name' => $team->name->value,
            'slug' => $team->slug->value,
            'description' => $team->description ?? '',
            default => '',
        };
    }

    private function searchMatcher(Team $team, string $term): bool
    {
        $normalizedTerm = mb_strtolower($this->stripDiacriticsForFilter($term));

        return str_contains($this->stripDiacriticsForFilter(mb_strtolower($team->name->value)), $normalizedTerm)
            || str_contains($this->stripDiacriticsForFilter(mb_strtolower($team->slug->value)), $normalizedTerm)
            || str_contains($this->stripDiacriticsForFilter(mb_strtolower($team->description ?? '')), $normalizedTerm);
    }
}
