<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Sso;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;

final readonly class EloquentSsoConfigurationRepository implements SsoConfigurationRepository
{
    public function __construct(
        private SsoConfigurationMapper $ssoConfigurationMapper,
    ) {}

    public function all(): array
    {
        return array_values(
            SsoConfigurationModel::query()
                ->orderBy('provider_type')
                ->orderBy('slug')
                ->get()
                ->map(fn (SsoConfigurationModel $ssoConfigurationModel): SsoConfiguration => $this->ssoConfigurationMapper->toDomain($ssoConfigurationModel))
                ->all(),
        );
    }

    public function allEnabled(): array
    {
        return array_values(
            SsoConfigurationModel::query()
                ->where('enabled', true)
                ->orderBy('display_name')
                ->get()
                ->map(fn (SsoConfigurationModel $ssoConfigurationModel): SsoConfiguration => $this->ssoConfigurationMapper->toDomain($ssoConfigurationModel))
                ->all(),
        );
    }

    public function findById(SsoConfigurationId $ssoConfigurationId): ?SsoConfiguration
    {
        $model = SsoConfigurationModel::query()->find($ssoConfigurationId->value);

        return $model instanceof SsoConfigurationModel ? $this->ssoConfigurationMapper->toDomain($model) : null;
    }

    public function findBySlug(ProviderType $providerType, string $slug): ?SsoConfiguration
    {
        $model = SsoConfigurationModel::query()
            ->where('provider_type', $providerType->value)
            ->where('slug', $slug)
            ->first();

        return $model instanceof SsoConfigurationModel ? $this->ssoConfigurationMapper->toDomain($model) : null;
    }

    public function hasEnforcedConfiguration(): bool
    {
        return SsoConfigurationModel::query()
            ->where('enabled', true)
            ->where('enforce', true)
            ->exists();
    }

    public function create(SsoConfiguration $ssoConfiguration): void
    {
        $ssoConfigurationModel = new SsoConfigurationModel;
        $ssoConfigurationModel->id = $ssoConfiguration->id->value;
        $ssoConfigurationModel->fill($this->toAttributes($ssoConfiguration));
        $ssoConfigurationModel->save();
    }

    public function update(SsoConfiguration $ssoConfiguration): void
    {
        $ssoConfigurationModel = SsoConfigurationModel::query()->findOrFail($ssoConfiguration->id->value);
        $ssoConfigurationModel->fill($this->toAttributes($ssoConfiguration));
        $ssoConfigurationModel->save();
    }

    public function delete(SsoConfigurationId $ssoConfigurationId): void
    {
        SsoConfigurationModel::query()->where('id', $ssoConfigurationId->value)->delete();
    }

    /** @return array<string, scalar|array<int|string, mixed>> */
    private function toAttributes(SsoConfiguration $ssoConfiguration): array
    {
        return [
            'provider_type' => $ssoConfiguration->providerType->value,
            'slug' => $ssoConfiguration->slug,
            'display_name' => $ssoConfiguration->displayName,
            'enabled' => $ssoConfiguration->enabled,
            'enforce' => $ssoConfiguration->enforce,
            'jit_mode' => $ssoConfiguration->jitMode->value,
            'allowed_email_domains' => $ssoConfiguration->allowedEmailDomains->domains,
            'config' => $ssoConfiguration->config,
        ];
    }
}
