<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\User;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;
use App\Infrastructure\Eloquent\PaginatesQuery;
use App\Infrastructure\Eloquent\SortsQuery;
use Illuminate\Database\Eloquent\Builder;

final readonly class EloquentUserRepository implements UserRepository
{
    use PaginatesQuery;
    use SortsQuery;

    public function __construct(
        private UserMapper $userMapper,
    ) {}

    /** @return list<User> */
    public function all(?array $onlyIds = null, array $sortings = []): array
    {
        $builder = $this->sortBuilder($this->baseQuery($onlyIds), $sortings);

        return array_values(
            $builder->get()
                ->map(fn (UserModel $userModel): User => $this->userMapper->toDomain($userModel))
                ->all(),
        );
    }

    /** @return PaginatedResult<User> */
    public function allPaginated(Pagination $pagination, ?array $onlyIds = null, array $sortings = []): PaginatedResult
    {
        $builder = $this->sortBuilder($this->baseQuery($onlyIds), $sortings);

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
        $userModel = new UserModel;
        $userModel->id = $user->id->value;
        $userModel->name = $user->name;
        $userModel->email = $user->email->value;
        $userModel->save();
    }

    public function update(User $user): void
    {
        $userModel = UserModel::findOrFail($user->id->value);
        $userModel->name = $user->name;
        $userModel->email = $user->email->value;
        $userModel->save();
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
