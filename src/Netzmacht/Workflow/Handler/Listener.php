<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;

/**
 * Interface Listener describes an listener for the transition hander.
 *
 * @package Netzmacht\Workflow\Handler\Dispatcher
 */
interface Listener
{
    /**
     * Consider if form is validated.
     *
     * @param Form     $form           Transition form.
     * @param bool     $validated      Current validation state.
     * @param Workflow $workflow       Current workflow.
     * @param Item     $item           Workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Current transition name.
     *
     * @return bool
     */
    public function onValidate(
        Form $form,
        $validated,
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    );

    /**
     * Dispatch pre transition.
     *
     * @param Workflow $workflow       The workflow.
     * @param Item     $item           Current workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Transition name.
     *
     * @return void
     */
    public function onPreTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    );

    /**
     * Dispatch post transition.
     *
     * @param Workflow $workflow The workflow.
     * @param Item     $item     Current workflow item.
     * @param Context  $context  Transition context.
     * @param State    $state    Item state.
     *
     * @return void
     */
    public function onPostTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        State $state
    );

    /**
     * Dispatch build form.
     *
     * @param Form     $form           Form being build.
     * @param Workflow $workflow       Current workflow.
     * @param Item     $item           Workflow item.
     * @param Context  $context        Transition context.
     * @param string   $transitionName Transition name.
     *
     * @return void
     */
    public function onBuildForm(Form $form, Workflow $workflow, Item $item, Context $context, $transitionName);
}
