<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class OrCondition matches if any child conditions matches.
 *
 * @package Netzmacht\Workflow\Flow\Workflow
 */
class OrCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity): bool
    {
        foreach ($this->conditions as $condition) {
            if ($condition->match($workflow, $entityId, $entity)) {
                return true;
            }
        }

        if (!$this->conditions) {
            return true;
        }

        return false;
    }
}
