<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Handler\Listener;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Listener;

/**
 * Class NoOpDispatcher is an dispatcher for the transition handler which does nothing.
 *
 * @package Netzmacht\Workflow\Handler\Dispatcher
 */
class NoOpListener implements Listener
{
    /**
     * {@inheritdoc}
     */
    public function onBuildForm(Form $form, Workflow $workflow, Item $item, Context $context, $transitionName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onValidate(
        Form $form,
        $validated,
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    ) {
        return $validated;
    }

    /**
     * {@inheritdoc}
     */
    public function onPreTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function onPostTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        State $state
    ) {
    }
}
