<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrganizationMemberModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'organization_members';

    protected $fillable = ['id', 'user_id', 'organization_id', 'joined_at'];

    protected $keyType = 'string';

    /** @return BelongsTo<UserModel, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /** @return BelongsTo<OrganizationModel, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationModel::class, 'organization_id');
    }
}
