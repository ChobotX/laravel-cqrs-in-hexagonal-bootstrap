<?php

declare(strict_types=1);

namespace App\Contract\Query;

/**
 * Marker for a read-side message handled by the query bus. Concrete queries declare their result type via `@implements Query<TResult>`.
 *
 * @template TResult
 */
interface Query {}
