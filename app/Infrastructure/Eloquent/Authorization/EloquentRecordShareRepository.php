<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Domain\Authorization\Action;
use App\Domain\Authorization\Contract\RecordShareRepository;
use App\Domain\Authorization\RecordShare;

final readonly class EloquentRecordShareRepository implements RecordShareRepository
{
    public function __construct(
        private RecordShareMapper $recordShareMapper,
    ) {}

    public function share(RecordShare $recordShare): void
    {
        $recordShareModel = new RecordShareModel;
        $recordShareModel->grantee_user_id = $recordShare->granteeUserId;
        $recordShareModel->resource_type = $recordShare->resourceType;
        $recordShareModel->resource_id = $recordShare->resourceId;
        $recordShareModel->action = $recordShare->action->value;
        $recordShareModel->grantor_user_id = $recordShare->grantorUserId;
        $recordShareModel->save();
    }

    public function revoke(string $granteeUserId, string $resourceType, string $resourceId): void
    {
        RecordShareModel::where('grantee_user_id', $granteeUserId)
            ->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->delete();
    }

    public function exists(string $granteeUserId, string $resourceType, string $resourceId): bool
    {
        return RecordShareModel::where('grantee_user_id', $granteeUserId)
            ->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->exists();
    }

    /** @return list<RecordShare> */
    public function findByGrantee(string $granteeUserId, ?string $resourceType = null): array
    {
        $query = RecordShareModel::where('grantee_user_id', $granteeUserId);

        if ($resourceType !== null) {
            $query->where('resource_type', $resourceType);
        }

        return array_values(
            $query->get()
                ->map(fn (RecordShareModel $recordShareModel): RecordShare => $this->recordShareMapper->toDomain($recordShareModel))
                ->all(),
        );
    }

    /** @return list<string> */
    public function accessibleResourceIds(
        string $granteeUserId,
        string $resourceType,
        Action $action,
    ): array {
        /** @var list<string> $resourceIds */
        $resourceIds = array_values(
            RecordShareModel::where('grantee_user_id', $granteeUserId)
                ->where('resource_type', $resourceType)
                ->where('action', $action->value)
                ->pluck('resource_id')
                ->all(),
        );

        return $resourceIds;
    }
}
