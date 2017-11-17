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

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

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
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        if (!$this->conditions) {
            return true;
        }

        $localContext = $context->createCleanCopy();

        foreach ($this->conditions as $condition) {
            if ($condition->match($transition, $item, $localContext)) {
                return true;
            }
        }

        $context->addError('transition.condition.or.failed', array(), $localContext->getErrorCollection());

        return false;
    }
}
