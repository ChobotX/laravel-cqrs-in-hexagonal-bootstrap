<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\User;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\QueryBus;
use App\Domain\User\Contract\Query\ListUsersGridQuery;
use App\Domain\User\Contract\ValueObject\UserGridLabel;
use App\Domain\User\Contract\ValueObject\UserGridRoleLabel;
use App\Domain\User\Contract\ValueObject\UserGridRow;
use App\Domain\User\Contract\ValueObject\UserGridTeamLabel;
use App\Domain\User\Contract\ValueObject\UsersGridResult;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('users.list.read')]
final readonly class ListUsersGridController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): JsonResponse
    {
        /** @var UsersGridResult $usersGridResult */
        $usersGridResult = $this->queryBus->dispatch(new ListUsersGridQuery(
            pagination: $paginationRequest->pagination(),
            sorting: $paginationRequest->sorting(),
            search: $paginationRequest->search(),
            actingUserId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse([
            'data' => array_map($this->serializeRow(...), $usersGridResult->rows),
            'meta' => [
                'current_page' => $usersGridResult->page,
                'per_page' => $usersGridResult->perPage,
                'total' => $usersGridResult->total,
                'total_pages' => $usersGridResult->totalPages,
            ],
            'permissions' => [
                'can_create' => $usersGridResult->permissions->canCreate,
                'can_update' => $usersGridResult->permissions->canUpdate,
                'can_delete' => $usersGridResult->permissions->canDelete,
                'is_super_admin' => $usersGridResult->permissions->isSuperAdmin,
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function serializeRow(UserGridRow $userGridRow): array
    {
        return [
            'id' => $userGridRow->id,
            'name' => $userGridRow->name,
            'email' => $userGridRow->email,
            'avatar_url' => $userGridRow->avatarFileId !== null ? route('files.show', $userGridRow->avatarFileId) : null,
            'initials' => $userGridRow->initials,
            'roles' => $userGridRow->roles !== null
                ? array_map(fn (UserGridRoleLabel $userGridRoleLabel): array => [
                    'id' => $userGridRoleLabel->id,
                    'name' => $userGridRoleLabel->name,
                    'url' => route('roles.show', $userGridRoleLabel->id),
                    'is_system' => $userGridRoleLabel->isSystem,
                ], $userGridRow->roles)
                : null,
            'teams' => $userGridRow->teams !== null
                ? array_map(fn (UserGridTeamLabel $userGridTeamLabel): array => [
                    'id' => $userGridTeamLabel->id,
                    'name' => $userGridTeamLabel->name,
                    'url' => route('teams.show', $userGridTeamLabel->id),
                ], $userGridRow->teams)
                : null,
            'labels' => $userGridRow->labels !== null
                ? array_map(fn (UserGridLabel $userGridLabel): array => [
                    'id' => $userGridLabel->id,
                    'name' => $userGridLabel->name,
                ], $userGridRow->labels)
                : null,
            'edit_url' => route('users.edit', $userGridRow->id),
            'delete_url' => route('users.destroy', $userGridRow->id),
            'impersonate_url' => $userGridRow->impersonable ? route('impersonation.start', $userGridRow->id) : null,
        ];
    }
}
