<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Transition;

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
     * @param \Netzmacht\Workflow\Flow\Transition $transition The transition being in.
     * @param \Netzmacht\Workflow\Flow\Item       $item       The entity being transits.
     * @param \Netzmacht\Workflow\Flow\Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context);
}
