<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Workflow;

use Netzmacht\Workflow\Data\Entity;
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
     * @param Entity   $entity   The entity.
     *
     * @return bool
     */
    public function match(Workflow $workflow, Entity $entity);
}
