<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\FeatureFlag;

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\Repository\FeatureFlagOverrideRepository;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;

final readonly class EloquentFeatureFlagOverrideRepository implements FeatureFlagOverrideRepository
{
    public function __construct(
        private FeatureFlagOverrideMapper $featureFlagOverrideMapper,
    ) {}

    /**
     * @return list<FeatureFlagOverride>
     */
    public function findAll(): array
    {
        $models = FeatureFlagOverrideModel::all();

        $result = [];
        foreach ($models as $model) {
            $result[] = $this->featureFlagOverrideMapper->toDomain($model);
        }

        return $result;
    }

    public function findByKey(FlagKey $flagKey): ?FeatureFlagOverride
    {
        $model = FeatureFlagOverrideModel::find($flagKey->value);

        if (! $model instanceof FeatureFlagOverrideModel) {
            return null;
        }

        return $this->featureFlagOverrideMapper->toDomain($model);
    }

    public function save(FeatureFlagOverride $featureFlagOverride): void
    {
        $model = FeatureFlagOverrideModel::find($featureFlagOverride->key);

        if ($model instanceof FeatureFlagOverrideModel) {
            $model->enabled = $featureFlagOverride->enabled;
            $model->value = $featureFlagOverride->value;
            $model->save();

            return;
        }

        $model = new FeatureFlagOverrideModel;
        $model->key = $featureFlagOverride->key;
        $model->enabled = $featureFlagOverride->enabled;
        $model->value = $featureFlagOverride->value;
        $model->save();
    }

    public function delete(FlagKey $flagKey): void
    {
        FeatureFlagOverrideModel::where('key', $flagKey->value)->delete();
    }
}
