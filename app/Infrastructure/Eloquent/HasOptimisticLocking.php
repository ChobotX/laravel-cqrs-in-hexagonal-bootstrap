<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasOptimisticLocking
{
    public static function bootHasOptimisticLocking(): void
    {
        static::creating(function (Model $model): void {
            if (! isset($model->attributes['version'])) {
                $model->version = 1;
            }
        });
    }

    public function initializeHasOptimisticLocking(): void
    {
        $this->mergeFillable(['version']);
    }

    /**
     * @param  Builder<static>  $builder
     */
    protected function performUpdate(Builder $builder): bool
    {
        if ($this->fireModelEvent('updating') === false) {
            return false;
        }

        if ($this->usesTimestamps()) {
            $this->updateTimestamps();
        }

        $dirty = $this->getDirty();

        if ($dirty !== []) {
            /** @var int $currentVersion */
            $currentVersion = $this->version;
            $newVersion = $currentVersion + 1;
            $dirty['version'] = $newVersion;

            $affected = $this->setKeysForSaveQuery($builder)
                ->where('version', $currentVersion)
                ->update($dirty);

            if ($affected === 0) {
                /** @var string $key */
                $key = $this->getKey();

                throw new ConcurrentModificationException(static::class, $key);
            }

            $this->version = $newVersion;
            $this->syncChanges();
            $this->fireModelEvent('updated', false);
        }

        return true;
    }
}
