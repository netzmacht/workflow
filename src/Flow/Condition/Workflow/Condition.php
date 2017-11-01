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

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Interface Condition describes condition being used by the workflow.
 *
 * @package Netzmacht\Workflow\Flow\Workflow
 */
interface Condition
{
    /**
     * Consider if workflow matches to the entity.
     *
     * @param Workflow $workflow The current workflow.
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     *
     * @return bool
     */
    public function match(Workflow $workflow, EntityId $entityId, $entity);
}
