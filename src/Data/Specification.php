<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Data;

/**
 * Data specification.
 */
interface Specification
{
    /**
     * Check if an entity matches the specification.
     *
     * @param mixed $entity The entity.
     */
    public function isSatisfiedBy(mixed $entity): bool;
}
