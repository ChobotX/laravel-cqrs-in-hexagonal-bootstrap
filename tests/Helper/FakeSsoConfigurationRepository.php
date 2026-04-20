<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;

final class FakeSsoConfigurationRepository implements SsoConfigurationRepository
{
    /** @var list<SsoConfiguration> */
    public array $created = [];

    /** @var list<SsoConfiguration> */
    public array $updated = [];

    /** @var list<string> */
    public array $deleted = [];

    /** @param array<string, SsoConfiguration> $items */
    public function __construct(
        private array $items = [],
    ) {}

    public function all(): array
    {
        return array_values($this->items);
    }

    public function allEnabled(): array
    {
        return array_values(array_filter($this->items, fn (SsoConfiguration $ssoConfiguration): bool => $ssoConfiguration->enabled));
    }

    public function findById(SsoConfigurationId $ssoConfigurationId): ?SsoConfiguration
    {
        return $this->items[$ssoConfigurationId->value] ?? null;
    }

    public function findBySlug(ProviderType $providerType, string $slug): ?SsoConfiguration
    {
        foreach ($this->items as $item) {
            if ($item->providerType === $providerType && $item->slug === $slug) {
                return $item;
            }
        }

        return null;
    }

    public function hasEnforcedConfiguration(): bool
    {
        return array_any($this->items, fn ($item): bool => $item->enabled && $item->enforce);
    }

    public function create(SsoConfiguration $ssoConfiguration): void
    {
        $this->created[] = $ssoConfiguration;
        $this->items[$ssoConfiguration->id->value] = $ssoConfiguration;
    }

    public function update(SsoConfiguration $ssoConfiguration): void
    {
        $this->updated[] = $ssoConfiguration;
        $this->items[$ssoConfiguration->id->value] = $ssoConfiguration;
    }

    public function delete(SsoConfigurationId $ssoConfigurationId): void
    {
        $this->deleted[] = $ssoConfigurationId->value;
        unset($this->items[$ssoConfigurationId->value]);
    }
}
