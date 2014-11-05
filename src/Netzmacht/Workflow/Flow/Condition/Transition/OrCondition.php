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
 * Class OrCondition matches if any of the child condition matches.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
class OrCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        if (!$this->conditions) {
            return $this->pass();
        }

        foreach ($this->conditions as $condition) {
            if ($condition->match($transition, $item, $context)) {
                return $this->pass();
            } else {
                $this->addError($condition);
            }
        }

        return $this->fail('transition.condition.or');
    }
}
