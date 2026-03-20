<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Organization;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class TeamModel extends Model
{
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'teams';

    protected $fillable = ['id', 'organization_id', 'parent_team_id', 'name', 'slug', 'description'];

    protected $keyType = 'string';

    /** @return BelongsTo<OrganizationModel, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationModel::class, 'organization_id');
    }

    /** @return BelongsTo<self, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_team_id');
    }

    /** @return HasMany<self, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_team_id');
    }

    /** @return HasMany<TeamMemberModel, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(TeamMemberModel::class, 'team_id');
    }
}
