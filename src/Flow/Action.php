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

namespace Netzmacht\Workflow\Flow;

/**
 * Interface Action describes an action which is executed during transition.
 *
 * @package Netzmacht\Workflow
 */
interface Action
{
    /**
     * Get the required payload properties.
     *
     * @param Item $item Workflow item.
     *
     * @return array
     */
    public function getRequiredPayloadProperties(Item $item): array;

    /**
     * Validate the given item and context (payload properties).
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     *
     * @return bool
     */
    public function validate(Item $item, Context $context): bool;

    /**
     * Transit will execute the action.
     *
     * @param Transition $transition Current transition.
     * @param Item       $item       Workflow item.
     * @param Context    $context    Transition context.
     *
     * @return void
     */
    public function transit(Transition $transition, Item $item, Context $context): void;
}
