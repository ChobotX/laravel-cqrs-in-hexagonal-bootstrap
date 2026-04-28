<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;

final class FakeTenantMailTransportRepository implements TenantMailTransportRepository
{
    public ?MailTransport $captured = null;

    public bool $cleared = false;

    public function __construct(
        public ?MailTransport $custom = null,
        public ?MailTransport $defaultTransport = null,
    ) {}

    public function findCustom(): ?MailTransport
    {
        return $this->custom;
    }

    public function default(): MailTransport
    {
        return $this->defaultTransport ?? new MailTransport(
            provider: MailProvider::Custom,
            host: 'mailpit',
            port: 1025,
            username: null,
            password: null,
            encryption: null,
            fromAddress: 'no-reply@example.com',
            fromName: 'Test',
            isCustom: false,
        );
    }

    public function save(MailTransport $mailTransport): void
    {
        $this->captured = $mailTransport;
        $this->custom = $mailTransport;
    }

    public function clear(): void
    {
        $this->cleared = true;
        $this->custom = null;
    }
}
