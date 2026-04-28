<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Application\Event\PropertyChange;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Tenancy\Contract\Command\UpdateTenantMailTransportCommand;
use App\Domain\Tenancy\Contract\Enum\MailProvider;
use App\Domain\Tenancy\Contract\Event\TenantMailTransportUpdated;
use App\Domain\Tenancy\Contract\Repository\TenantMailTransportRepository;
use App\Domain\Tenancy\Contract\ValueObject\MailTransport;
use App\Domain\Tenancy\Exception\InvalidMailTransportException;
use DateTimeImmutable;

use function filter_var;
use function in_array;
use function trim;

use const FILTER_VALIDATE_EMAIL;

/** @implements CommandHandler<UpdateTenantMailTransportCommand> */
final readonly class UpdateTenantMailTransportHandler implements CommandHandler
{
    private const int MIN_PORT = 1;

    private const int MAX_PORT = 65535;

    public function __construct(
        private TenantMailTransportRepository $tenantMailTransportRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->tenantMailTransportRepository->findCustom();

        if (! $command->useCustom) {
            $this->revertToDefault($command->tenantId, $existing);

            return;
        }

        $mailTransport = $this->buildTransport($command, $existing);

        if ($existing instanceof MailTransport && $this->equal($existing, $mailTransport)) {
            return;
        }

        $this->tenantMailTransportRepository->save($mailTransport);
        $this->eventCollector->collect($this->event($command->tenantId, $this->diff($existing, $mailTransport)));
    }

    private function revertToDefault(string $tenantId, ?MailTransport $mailTransport): void
    {
        if (! $mailTransport instanceof MailTransport) {
            return;
        }

        $this->tenantMailTransportRepository->clear();
        $this->eventCollector->collect($this->event($tenantId, [
            new PropertyChange(property: 'is_custom', old: true, new: false),
        ]));
    }

    private function buildTransport(UpdateTenantMailTransportCommand $updateTenantMailTransportCommand, ?MailTransport $mailTransport): MailTransport
    {
        $host = trim((string) $updateTenantMailTransportCommand->host);
        $fromAddress = trim((string) $updateTenantMailTransportCommand->fromAddress);
        $fromName = trim((string) $updateTenantMailTransportCommand->fromName);

        $this->guardAgainstInvalidInput($updateTenantMailTransportCommand, $host, $fromAddress, $fromName);

        return new MailTransport(
            provider: $updateTenantMailTransportCommand->provider ?? MailProvider::Custom,
            host: $host,
            port: (int) $updateTenantMailTransportCommand->port,
            username: $this->normalizeUsername($updateTenantMailTransportCommand->username),
            password: $this->resolvePassword($updateTenantMailTransportCommand->password, $mailTransport),
            encryption: $updateTenantMailTransportCommand->encryption,
            fromAddress: $fromAddress,
            fromName: $fromName,
            isCustom: true,
        );
    }

    private function guardAgainstInvalidInput(UpdateTenantMailTransportCommand $updateTenantMailTransportCommand, string $host, string $fromAddress, string $fromName): void
    {
        if ($host === '') {
            throw new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_host');
        }

        if ($updateTenantMailTransportCommand->port === null || $updateTenantMailTransportCommand->port < self::MIN_PORT || $updateTenantMailTransportCommand->port > self::MAX_PORT) {
            throw new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_port');
        }

        if ($fromAddress === '' || filter_var($fromAddress, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_from_address');
        }

        if ($fromName === '') {
            throw new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_from_name');
        }

        if ($updateTenantMailTransportCommand->encryption !== null && ! in_array($updateTenantMailTransportCommand->encryption, MailTransport::ALLOWED_ENCRYPTIONS, true)) {
            throw new InvalidMailTransportException('messages.exceptions.invalid_mail_transport_encryption');
        }
    }

    private function normalizeUsername(?string $username): ?string
    {
        if ($username === null) {
            return null;
        }

        $trimmed = trim($username);

        return $trimmed === '' ? null : $trimmed;
    }

    private function resolvePassword(?string $incoming, ?MailTransport $mailTransport): ?string
    {
        if ($incoming === null || $incoming === '') {
            return $mailTransport?->password;
        }

        return $incoming;
    }

    private function equal(MailTransport $a, MailTransport $b): bool
    {
        return $a->provider === $b->provider
            && $a->host === $b->host
            && $a->port === $b->port
            && $a->username === $b->username
            && $a->password === $b->password
            && $a->encryption === $b->encryption
            && $a->fromAddress === $b->fromAddress
            && $a->fromName === $b->fromName;
    }

    /** @return list<PropertyChange> */
    private function diff(?MailTransport $existing, MailTransport $next): array
    {
        $changes = $this->scalarChanges($existing, $next);

        if ($existing?->password !== $next->password) {
            $changes[] = new PropertyChange(property: 'password', old: null, new: null, sensitive: true);
        }

        if (! $existing instanceof MailTransport) {
            $changes[] = new PropertyChange(property: 'is_custom', old: false, new: true);
        }

        return $changes;
    }

    /** @return list<PropertyChange> */
    private function scalarChanges(?MailTransport $existing, MailTransport $next): array
    {
        /** @var list<array{string, string|int|null, string|int|null}> $pairs */
        $pairs = [
            ['provider', $existing?->provider->value, $next->provider->value],
            ['host', $existing?->host, $next->host],
            ['port', $existing?->port, $next->port],
            ['username', $existing?->username, $next->username],
            ['encryption', $existing?->encryption, $next->encryption],
            ['from_address', $existing?->fromAddress, $next->fromAddress],
            ['from_name', $existing?->fromName, $next->fromName],
        ];

        $changes = [];

        foreach ($pairs as [$property, $old, $new]) {
            if ($old !== $new) {
                $changes[] = new PropertyChange(property: $property, old: $old, new: $new);
            }
        }

        return $changes;
    }

    /** @param list<PropertyChange> $changes */
    private function event(string $tenantId, array $changes): TenantMailTransportUpdated
    {
        return new TenantMailTransportUpdated(
            tenantId: $tenantId,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        );
    }
}
