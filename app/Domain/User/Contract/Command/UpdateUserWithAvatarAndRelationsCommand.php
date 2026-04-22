<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;
use App\Domain\File\Contract\ValueObject\FileUpload;

/**
 * Orchestrates avatar storage, user update, role/team/label sync via one controller dispatch.
 * Handler fans out to {@see \App\Domain\File\Contract\Command\StoreAvatarCommand},
 * {@see UpdateUserCommand}, {@see \App\Domain\Authorization\Contract\Command\SyncUserRolesCommand},
 * {@see \App\Domain\Team\Contract\Command\SyncUserTeamsCommand} and
 * {@see \App\Domain\Label\Contract\Command\SyncEntityLabelsCommand}.
 *
 * Relation lists are nullable to distinguish "no change" (null) from "remove all" ([]).
 */
#[RequiresPermission('users.list.update')]
final readonly class UpdateUserWithAvatarAndRelationsCommand implements Command
{
    /**
     * @param  list<string>|null  $roleIds  null = leave untouched, [] = sync empty
     * @param  list<string>|null  $teamIds  null = leave untouched, [] = sync empty
     * @param  list<string>|null  $labelIds  null = leave untouched, [] = sync empty
     */
    public function __construct(
        /** Target user being updated. */
        public string $id,
        /** Human-visible label or title. */
        public string $name,
        /** New email or null to keep existing. */
        public ?string $email,
        /** Actor performing the change; used for uploads and sync auditing. */
        public string $actorId,
        /** Optional avatar upload; ignored when $removeAvatar is true. */
        public ?FileUpload $avatarUpload = null,
        /** When true, clears the current avatar (takes precedence over $avatarUpload). */
        public bool $removeAvatar = false,
        public ?array $roleIds = null,
        public ?array $teamIds = null,
        public ?array $labelIds = null,
    ) {}
}
