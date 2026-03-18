<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

use App\Domain\Authorization\Exception\InvalidPermissionKeyException;
use Stringable;

final readonly class PermissionKey implements Stringable
{
    public function __construct(
        public Module $module,
        public ?Feature $feature = null,
        public ?Action $action = null,
    ) {
        if ($this->action instanceof Action && ! $this->feature instanceof Feature) {
            throw new InvalidPermissionKeyException($this->__toString());
        }
    }

    public function __toString(): string
    {
        $parts = [$this->module->value];

        if ($this->feature instanceof Feature) {
            $parts[] = $this->feature->value;
        }

        if ($this->action instanceof Action) {
            $parts[] = $this->action->value;
        }

        return implode('.', $parts);
    }

    public function covers(self $other): bool
    {
        if (! $this->module->equals($other->module)) {
            return false;
        }

        if (! $this->feature instanceof Feature) {
            return true;
        }

        if (! $other->feature instanceof Feature || ! $this->feature->equals($other->feature)) {
            return false;
        }

        if (! $this->action instanceof Action) {
            return true;
        }

        return $other->action instanceof Action && $this->action === $other->action;
    }

    public function equals(self $other): bool
    {
        return $this->__toString() === $other->__toString();
    }

    public function isLeaf(): bool
    {
        return $this->feature instanceof Feature && $this->action instanceof Action;
    }
}
