<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class AndCondition matches if all child conditions matches.
 *
 * @package Netzmacht\Workflow\Flow\Workflow
 */
class AndCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity)
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->match($workflow, $entityId, $entity)) {
                return false;
            }
        }

        return true;
    }
}
