<?php

declare(strict_types=1);

/**
 * Maps `App\Infrastructure\{TopLevelSegment}\…` folder names that are not identical to a Domain
 * module name to their owning `App\Domain\{Module}` module. Used by
 * {@see Tests\Architecture\PHPStan\InfrastructureCrossDomainImportBoundary} and
 * {@see Tests\Architecture\InfrastructureDomainAliasMappingTest}.
 *
 * Prefer **renaming** `app/Infrastructure/{Segment}` to match `app/Domain/{Module}` and keeping
 * this list empty. Entries here are only for segments that cannot align (legacy, third-party,
 * or shared buckets).
 *
 * @return array<string, string>
 */
return [];
