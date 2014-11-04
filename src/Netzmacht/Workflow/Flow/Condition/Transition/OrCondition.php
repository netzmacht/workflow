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

class OrCondition extends ConditionCollection
{
    /**
     * Consider if condition matches for the given entity.
     *
     * @param \Netzmacht\Workflow\Flow\Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        if (!$this->conditions) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if ($condition->match($transition, $item, $context)) {
                return true;
            }
        }

        return false;
    }
}
