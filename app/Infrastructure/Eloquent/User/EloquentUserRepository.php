<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;
use App\Infrastructure\Eloquent\FiltersQuery;
use App\Infrastructure\Eloquent\PaginatesQuery;
use App\Infrastructure\Eloquent\SortsQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;

final readonly class EloquentUserRepository implements UserRepository
{
    use FiltersQuery;
    use PaginatesQuery;
    use SortsQuery;

    public function __construct(
        private UserMapper $userMapper,
    ) {}

    /** @return list<User> */
    public function all(?array $onlyIds = null, array $sortings = [], array $filters = []): array
    {
        $builder = $this->filterBuilder($this->sortBuilder($this->baseQuery($onlyIds), $sortings), $filters);

        return array_values(
            $builder->get()
                ->map(fn (UserModel $userModel): User => $this->userMapper->toDomain($userModel))
                ->all(),
        );
    }

    /** @return PaginatedResult<User> */
    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = [], array $filters = []): PaginatedResult
    {
        $builder = $this->filterBuilder($this->sortBuilder($this->baseQuery($onlyIds), $sortings), $filters);

        [$models, $total] = $this->paginateBuilder($builder, $pagination);

        return new PaginatedResult(
            array_map($this->userMapper->toDomain(...), $models),
            $total,
            $pagination,
        );
    }

    public function findById(UserId $userId): ?User
    {
        $model = UserModel::find($userId->value);

        if ($model === null) {
            return null;
        }

        return $this->userMapper->toDomain($model);
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();

        if (! $model instanceof UserModel) {
            return null;
        }

        return $this->userMapper->toDomain($model);
    }

    public function create(User $user): void
    {
        try {
            $userModel = new UserModel;
            $userModel->id = $user->id->value;
            $userModel->name = $user->name->value;
            $userModel->email = $user->email->value;
            $userModel->avatar_file_id = $user->avatarFileId?->value;
            $userModel->save();
        } catch (UniqueConstraintViolationException) {
            throw new EmailAlreadyExistsException($user->email->value);
        }
    }

    public function update(User $user): void
    {
        try {
            $userModel = UserModel::findOrFail($user->id->value);
            $userModel->name = $user->name->value;
            $userModel->email = $user->email->value;
            $userModel->avatar_file_id = $user->avatarFileId?->value;
            $userModel->save();
        } catch (UniqueConstraintViolationException) {
            throw new EmailAlreadyExistsException($user->email->value);
        }
    }

    public function delete(UserId $userId): void
    {
        $model = UserModel::find($userId->value);

        if ($model instanceof UserModel) {
            $model->tokens()->delete();
            $model->delete();
        }
    }

    public function count(): int
    {
        return UserModel::count();
    }

    /** @return list<User> */
    public function search(string $term, array $excludeUserIds, int $limit, ?array $onlyIds = null): array
    {
        $builder = UserModel::query()
            ->where(function ($q) use ($term): void {
                $q->whereRaw('unaccent(name) ILIKE unaccent(?)', [sprintf('%%%s%%', $term)])
                    ->orWhereRaw('unaccent(email) ILIKE unaccent(?)', [sprintf('%%%s%%', $term)]);
            });

        if ($onlyIds !== null) {
            $builder->whereIn('id', $onlyIds);
        }

        if ($excludeUserIds !== []) {
            $builder->whereNotIn('id', $excludeUserIds);
        }

        return array_values(
            $builder->limit($limit)
                ->get()
                ->map(fn (UserModel $userModel): User => $this->userMapper->toDomain($userModel))
                ->all(),
        );
    }

    /** @return list<string> */
    private function textSortColumns(): array
    {
        return ['name', 'email'];
    }

    /** @return list<string> */
    private function searchColumns(): array
    {
        return ['name', 'email'];
    }

    /**
     * @param  list<string>|null  $onlyIds
     * @return Builder<UserModel>
     */
    private function baseQuery(?array $onlyIds): Builder
    {
        $query = UserModel::query();

        if ($onlyIds !== null) {
            $query->whereIn('id', $onlyIds);
        }

        return $query;
    }
}
