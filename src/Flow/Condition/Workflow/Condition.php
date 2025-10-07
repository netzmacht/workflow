<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface Condition describes condition being used by the workflow.
 */
interface Condition
{
    /**
     * Consider if workflow matches to the entity.
     *
     * @param Workflow $workflow The current workflow.
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     */
    public function match(Workflow $workflow, EntityId $entityId, mixed $entity): bool;
}
