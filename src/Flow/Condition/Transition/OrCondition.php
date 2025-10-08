<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Override;

/**
 * Class OrCondition matches if any of the child condition matches.
 */
final class OrCondition extends ConditionCollection
{
    #[Override]
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        $localContext = $context->createCleanCopy();

        foreach ($this->conditions as $condition) {
            if ($condition->match($transition, $item, $localContext)) {
                return true;
            }
        }

        $context->addError('transition.condition.or.failed', [], $localContext->getErrorCollection());

        return false;
    }
}
