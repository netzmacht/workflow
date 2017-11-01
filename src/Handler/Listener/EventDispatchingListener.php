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

namespace Netzmacht\Workflow\Handler\Listener;

use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Listener;
use Netzmacht\Workflow\Handler\Event\BuildFormEvent;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use Netzmacht\Workflow\Handler\Event\PreTransitionEvent;
use Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatchingListener listens to the transition handler and dispatches event for every call.
 *
 * @package Netzmacht\Workflow\Handler\Listener
 */
class EventDispatchingListener implements Listener
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function onBuildForm(Form $form, Workflow $workflow, Item $item, Context $context, $transitionName)
    {
        $event = new BuildFormEvent($form, $workflow, $item, $context, $transitionName);
        $this->eventDispatcher->dispatch($event::NAME, $event);
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
        $event = new ValidateTransitionEvent(
            $workflow,
            $transitionName,
            $item,
            $context,
            $form,
            $validated
        );

        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->isValid();
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
        $event = new PreTransitionEvent($workflow, $item, $context, $transitionName);
        $this->eventDispatcher->dispatch($event::NAME, $event);
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
        $event = new PostTransitionEvent($workflow, $item, $context, $state);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }
}
