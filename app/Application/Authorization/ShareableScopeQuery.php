<?php

declare(strict_types=1);

namespace App\Application\Authorization;

/**
 * Queries implementing this interface (alongside ScopeAwareQuery) receive
 * automatic record-share resolution via the ResolveScopeFilter bus middleware.
 * The middleware fetches resource IDs shared with the actor and populates
 * AccessContext::$sharedResourceIds so repositories can union them into
 * scope-filtered queries.
 */
interface ShareableScopeQuery
{
    /** Resource type string used in the record_shares table (e.g. 'entry'). */
    public function shareableResourceType(): string;
}
