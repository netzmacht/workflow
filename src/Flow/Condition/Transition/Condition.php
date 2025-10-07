<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;

/**
 * Interface Condition describes an condition which used for transition conditions.
 */
interface Condition
{
    /**
     * Consider if condition matches for the given entity.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     */
    public function match(Transition $transition, Item $item, Context $context): bool;
}
