<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use App\Infrastructure\Eloquent\Tenancy\TenantMailSettingsModel;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

use function is_array;
use function is_numeric;
use function is_string;

final readonly class EloquentTenantMailTransportRepository implements TenantMailTransportRepository
{
    private const string DEFAULT_FROM_ADDRESS = 'no-reply@localhost';

    private const string DEFAULT_FROM_NAME = 'Application';

    private const string DEFAULT_HOST = 'localhost';

    public function __construct(
        private ConfigRepository $configRepository,
    ) {}

    public function findCustom(): ?MailTransport
    {
        $row = TenantMailSettingsModel::query()->whereKey(TenantMailSettingsModel::SINGLETON_ID)->first();

        if (! $row instanceof TenantMailSettingsModel) {
            return null;
        }

        return new MailTransport(
            provider: MailProvider::tryFrom($this->stringAttr($row, 'provider')) ?? MailProvider::Custom,
            host: $this->stringAttr($row, 'host'),
            port: $this->intAttr($row, 'port'),
            username: $this->nullableStringAttr($row, 'username'),
            password: $this->nullableStringAttr($row, 'password'),
            encryption: $this->nullableStringAttr($row, 'encryption'),
            fromAddress: $this->stringAttr($row, 'from_address'),
            fromName: $this->stringAttr($row, 'from_name'),
            isCustom: true,
        );
    }

    public function default(): MailTransport
    {
        $mailerConfig = $this->mailerConfig();

        return new MailTransport(
            provider: MailProvider::Custom,
            host: $this->mailerString($mailerConfig, 'host', self::DEFAULT_HOST),
            port: $this->mailerInt($mailerConfig, 'port', MailProvider::SMTP_SUBMISSION_PORT),
            username: $this->mailerString($mailerConfig, 'username', null),
            password: $this->mailerString($mailerConfig, 'password', null),
            encryption: $this->mailerString($mailerConfig, 'encryption', null),
            fromAddress: $this->configString('mail.from.address', self::DEFAULT_FROM_ADDRESS),
            fromName: $this->configString('mail.from.name', self::DEFAULT_FROM_NAME),
            isCustom: false,
        );
    }

    public function save(MailTransport $mailTransport): void
    {
        TenantMailSettingsModel::query()->updateOrCreate(
            ['id' => TenantMailSettingsModel::SINGLETON_ID],
            [
                'provider' => $mailTransport->provider->value,
                'host' => $mailTransport->host,
                'port' => $mailTransport->port,
                'username' => $mailTransport->username,
                'password' => $mailTransport->password,
                'encryption' => $mailTransport->encryption,
                'from_address' => $mailTransport->fromAddress,
                'from_name' => $mailTransport->fromName,
            ],
        );
    }

    public function clear(): void
    {
        TenantMailSettingsModel::query()->whereKey(TenantMailSettingsModel::SINGLETON_ID)->delete();
    }

    private function stringAttr(TenantMailSettingsModel $tenantMailSettingsModel, string $name): string
    {
        $value = $tenantMailSettingsModel->getAttribute($name);

        return is_string($value) ? $value : '';
    }

    private function nullableStringAttr(TenantMailSettingsModel $tenantMailSettingsModel, string $name): ?string
    {
        $value = $tenantMailSettingsModel->getAttribute($name);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function intAttr(TenantMailSettingsModel $tenantMailSettingsModel, string $name): int
    {
        $value = $tenantMailSettingsModel->getAttribute($name);

        return is_numeric($value) ? (int) $value : 0;
    }

    /** @return array<array-key, mixed> */
    private function mailerConfig(): array
    {
        $defaultMailer = $this->configString('mail.default', 'smtp');
        $config = $this->configRepository->get('mail.mailers.'.$defaultMailer);

        return is_array($config) ? $config : [];
    }

    /**
     * @template T of string|null
     *
     * @param  array<array-key, mixed>  $config
     * @param  T  $fallback
     * @return string|T
     */
    private function mailerString(array $config, string $key, ?string $fallback): ?string
    {
        $value = $config[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : $fallback;
    }

    /** @param array<array-key, mixed> $config */
    private function mailerInt(array $config, string $key, int $fallback): int
    {
        $value = $config[$key] ?? null;

        return is_numeric($value) ? (int) $value : $fallback;
    }

    private function configString(string $key, string $fallback): string
    {
        $value = $this->configRepository->get($key, $fallback);

        return is_string($value) ? $value : $fallback;
    }
}
