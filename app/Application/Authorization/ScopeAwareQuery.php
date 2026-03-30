<?php

declare(strict_types=1);

namespace App\Application\Authorization;

/**
 * Queries implementing this interface receive automatic scope resolution
 * via the ResolveScopeFilter bus middleware. The middleware reads the
 * #[RequiresPermission] attribute, resolves the actor's AccessScope,
 * and creates a scoped copy of the query via withAccessContext().
 *
 * Implementors must also implement Query<TResult>.
 */
interface ScopeAwareQuery
{
    public function withAccessContext(AccessContext $accessContext): static;

    public function accessContext(): ?AccessContext;
}
