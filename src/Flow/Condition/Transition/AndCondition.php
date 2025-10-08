<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use Override;

/**
 * Class AndCondition matches if all child conditions does.
 */
final class AndCondition extends ConditionCollection
{
    #[Override]
    public function match(Transition $transition, Item $item, Context $context): bool
    {
        $localContext = $context->createCleanCopy();
        $success      = true;

        foreach ($this->conditions as $condition) {
            if ($condition->match($transition, $item, $localContext)) {
                continue;
            }

            $success = false;
        }

        if (! $success) {
            $context->addError(
                'transition.condition.and.failed',
                [],
                $localContext->getErrorCollection(),
            );
        }

        return $success;
    }
}
