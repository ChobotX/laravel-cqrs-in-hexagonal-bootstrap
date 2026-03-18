<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Builder;
use Override;

final class SoftDeleteAwareUserProvider extends EloquentUserProvider
{
    #[Override]
    protected function newModelQuery($model = null): Builder
    {
        return parent::newModelQuery($model)->whereNull('deleted_at');
    }
}
