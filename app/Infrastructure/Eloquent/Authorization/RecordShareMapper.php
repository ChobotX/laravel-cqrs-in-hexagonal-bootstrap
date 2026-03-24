<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Domain\Authorization\Action;
use App\Domain\Authorization\RecordShare;

final readonly class RecordShareMapper
{
    public function toDomain(RecordShareModel $recordShareModel): RecordShare
    {
        return new RecordShare(
            granteeUserId: $recordShareModel->grantee_user_id,
            resourceType: $recordShareModel->resource_type,
            resourceId: $recordShareModel->resource_id,
            action: Action::from($recordShareModel->action),
            grantorUserId: $recordShareModel->grantor_user_id,
        );
    }
}
