<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Form\Form;

/**
 * Interface Action describes an action which is executed during transition.
 *
 * @package Netzmacht\Workflow
 */
interface Action
{
    /**
     * Consider if user input is required.
     *
     * @return bool
     */
    public function requiresInputData();

    /**
     * Build the corresponding form.
     *
     * @param \Netzmacht\Workflow\Form\Form $form Transition form.
     * @param Item $item Workflow item.
     *
     * @return void
     */
    public function buildForm(Form $form, Item $item);

    /**
     * Transit will execute the action.
     *
     * @param Transition $transition Current transition.
     * @param Item       $item       Workflow item.
     * @param Context    $context    Transition context.
     *
     * @return void
     */
    public function transit(Transition $transition, Item $item, Context $context);
}
