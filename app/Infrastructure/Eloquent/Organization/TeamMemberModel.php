<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TeamMemberModel extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'team_members';

    protected $fillable = ['id', 'team_id', 'user_id', 'joined_at'];

    protected $keyType = 'string';

    /** @return BelongsTo<UserModel, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    /** @return BelongsTo<TeamModel, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(TeamModel::class, 'team_id');
    }
}
