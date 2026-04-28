<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\ValueObject;

use App\Domain\Tenancy\Contract\Enum\MailProvider;

/**
 * SMTP transport configuration used to send tenant-scoped emails. Covers Mailjet, Mailgun, Mailpit, and arbitrary SMTP.
 */
final readonly class MailTransport
{
    /** @var list<string> */
    public const array ALLOWED_ENCRYPTIONS = ['tls', 'ssl'];

    public function __construct(
        /** Provider identifier — selects preset (host/port/encryption) or `custom`. */
        public MailProvider $provider,
        /** SMTP host name. */
        public string $host,
        /** SMTP port. */
        public int $port,
        /** SMTP username, or null when the host accepts unauthenticated submission. */
        public ?string $username,
        /** SMTP password / API secret. */
        public ?string $password,
        /** `tls`, `ssl`, or null when the host doesn't require encryption. */
        public ?string $encryption,
        /** RFC 5322 from-address used for outgoing emails. */
        public string $fromAddress,
        /** Display name shown next to the from-address. */
        public string $fromName,
        /** True when the tenant has explicitly configured the transport. False = effective default. */
        public bool $isCustom,
    ) {}
}
