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
 * Class AndCondition matches if all child conditions does.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
class AndCondition extends ConditionCollection
{
    /**
     * {@inheritdoc}
     */
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $localContext = $context->createCleanCopy();
        $success      = true;

        foreach ($this->conditions as $condition) {
            if (!$condition->match($transition, $item, $localContext)) {
                $success = false;
            }
        }

        if (!$success) {
            $context->addError(
                'transition.condition.and.failed',
                [],
                $localContext->getErrorCollection()
            );
        }

        return $success;
    }
}
