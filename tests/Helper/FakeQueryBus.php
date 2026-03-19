<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Application\Bus\QueryBus;
use App\Contract\Query\Query;
use RuntimeException;
use Throwable;

final class FakeQueryBus implements QueryBus
{
    /** @param array<class-string, mixed> $results */
    public function __construct(private array $results = []) {}

    public function dispatch(Query $query): mixed
    {
        $class = $query::class;

        if (! array_key_exists($class, $this->results)) {
            throw new RuntimeException(sprintf('No fake result configured for query [%s].', $class));
        }

        $result = $this->results[$class];

        if ($result instanceof Throwable) {
            throw $result;
        }

        return $result;
    }
}
