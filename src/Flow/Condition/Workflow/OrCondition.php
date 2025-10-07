<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class OrCondition matches if any child conditions matches.
 */
class OrCondition extends ConditionCollection
{
    /**
     * {@inheritDoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        foreach ($this->conditions as $condition) {
            if ($condition->match($workflow, $entityId, $entity)) {
                return true;
            }
        }

        return ! $this->conditions;
    }
}
