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

use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Handler\Event\BuildFormEvent;
use Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use Netzmacht\Workflow\Handler\Event\PreTransitionEvent;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatchingTransitionHandler
 * @package Netzmacht\Workflow\Handler
 */
class EventDispatchingTransitionHandler extends AbstractTransitionHandler
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(
        Item $item,
        Workflow $workflow,
        $transitionName,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Context $context,
        EventDispatcher $eventDispatcher
    ) {
        parent::__construct(
            $item,
            $workflow,
            $transitionName,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $context
        );

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispatch build form.
     *
     * @param Form    $form Form being build.
     * @param Item    $item Workflow item.
     * @param Context $context
     * @param string  $transitionName
     *
     * @return void
     */
    protected function dispatchBuildForm(Form $form, Item $item, Context $context, $transitionName)
    {
        $event = new BuildFormEvent($form, $this->getWorkflow(), $item, $context, $transitionName);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }

    /**
     * Consider if form is validated.
     *
     * @param Form $form      Transition form.
     * @param bool $validated Current validation state
     *
     * @return bool
     */
    protected function dispatchValidate(Form $form, $validated)
    {
        $event = new ValidateTransitionEvent(
            $this->getWorkflow(),
            $this->getTransition()->getName(),
            $this->getItem(),
            $this->getContext(),
            $form,
            $validated
        );

        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->isValid();
    }

    /**
     * Dispatch pre transition.
     *
     * @param Workflow   $workflow       The workflow.
     * @param Item       $item           Current workflow item.
     * @param Context    $context        Transition context.
     * @param string     $transitionName Transition name.
     *
     * @return void
     */
    protected function dispatchPreTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $transitionName
    ) {
        $event = new PreTransitionEvent($workflow, $item, $context, $transitionName);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }

    /**
     * Dispatch post transition.
     *
     * @param Workflow $workflow The workflow.
     * @param Item     $item     Current workflow item.
     * @param Context  $context  Transition context.
     * @param          $state
     *
     * @return void
     */
    protected function dispatchPostTransit(
        Workflow $workflow,
        Item $item,
        Context $context,
        $state
    ) {
        $event = new PostTransitionEvent($workflow, $item, $context, $state);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }
}
