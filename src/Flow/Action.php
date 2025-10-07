<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

/**
 * Interface Action describes an action which is executed during transition.
 */
interface Action
{
    /**
     * Get the required payload properties.
     *
     * @param Item $item Workflow item.
     *
     * @return list<string>
     */
    public function getRequiredPayloadProperties(Item $item): array;

    /**
     * Validate the given item and context (payload properties).
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     */
    public function validate(Item $item, Context $context): bool;

    /**
     * Transit will execute the action.
     *
     * @param Transition $transition Current transition.
     * @param Item       $item       Workflow item.
     * @param Context    $context    Transition context.
     */
    public function transit(Transition $transition, Item $item, Context $context): void;
}
