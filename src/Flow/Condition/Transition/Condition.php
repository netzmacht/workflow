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

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;

/**
 * Interface Condition describes an condition which used for transtion conditions.
 *
 * @package Netzmacht\Workflow\Flow\Transition
 */
interface Condition
{
    /**
     * Consider if condition matches for the given entity.
     *
     * @param Transition      $transition      The transition being in.
     * @param Item            $item            The entity being transits.
     * @param Context         $context         The transition context.
     * @param ErrorCollection $errorCollection The error collection.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection);
}
