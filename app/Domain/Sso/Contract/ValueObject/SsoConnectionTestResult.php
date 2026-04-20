<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\ValueObject;

/**
 * Outcome of a non-interactive SsoConfiguration probe.
 *
 * For OIDC the discovery URL is fetched; for SAML the IdP metadata XML is parsed.
 * Social providers report success when the registered redirect URI resolves.
 */
final readonly class SsoConnectionTestResult
{
    /** @param list<string> $warnings */
    public function __construct(
        /** True when the IdP responded as expected. */
        public bool $success,
        /** Single-line summary suitable for the admin UI. */
        public string $summary,
        /** Optional non-fatal warnings (e.g. missing optional claim). */
        public array $warnings = [],
    ) {}
}
