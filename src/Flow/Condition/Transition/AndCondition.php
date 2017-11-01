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
 * Class AndCondition matches if all child conditions does.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
class AndCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection)
    {
        $errors  = new ErrorCollection();
        $success = true;

        foreach ($this->conditions as $condition) {
            if (!$condition->match($transition, $item, $context, $errors)) {
                $success = false;
            }
        }

        if (!$success) {
            $errorCollection->addError('transition.condition.and.failed', array(), $errors);
        }

        return $success;
    }
}
