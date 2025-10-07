<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Data;

use Netzmacht\Workflow\Flow\State;

/**
 * Interface StateRepository stores workflow states.
 */
interface StateRepository
{
    /**
     * Find last workflow state of an entity.
     *
     * @param EntityId $entityId The entity id.
     *
     * @return State[]|iterable
     */
    public function find(EntityId $entityId): iterable;

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     */
    public function add(State $state): void;
}
