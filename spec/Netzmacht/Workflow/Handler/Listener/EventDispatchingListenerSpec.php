<?php

namespace spec\Netzmacht\Workflow\Handler\Listener;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Listener\EventDispatchingListener;
use Netzmacht\Workflow\Handler\Event\BuildFormEvent;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use Netzmacht\Workflow\Handler\Event\PreTransitionEvent;
use Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcher;

/**
 * Class TransitionHandlerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin EventDispatchingListener
 */
class EventDispatchingListenerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';
    const CONTEXT_CLASS = 'Netzmacht\Workflow\Flow\Context';
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';
    const WORKFLOW_NAME = 'workflow_name';
    const STEP_NAME = 'step_name';

    protected static $entity = array('id' => 5);

    function let(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityRepository $entityRepository,
        TransactionHandler $transactionHandler,
        SymfonyEventDispatcher $eventDispatcher,
        Transition $transition,
        State $state,
        EntityId $entityId,
        Step $step
    )  {
        $workflow->getStep(static::STEP_NAME)->willReturn($step);
        $workflow->getStartTransition()->willReturn($transition);
        $workflow->getName()->willReturn(static::WORKFLOW_NAME);

        $step->isTransitionAllowed(static::TRANSITION_NAME)->willReturn(true);
        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->isInputRequired()->willReturn(false);
        $transition->transit(
            $item,
            Argument::type(static::CONTEXT_CLASS),
            Argument::type(static::ERROR_COLLECTION_CLASS)
        )->willReturn($state);

        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(static::STEP_NAME);
        $item->getEntity()->willReturn(static::$entity);

        $entityId->__toString()->willReturn('entity::2');

        $this->beConstructedWith($eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\Listener\EventDispatchingListener');
    }

    function it_is_a_tansition_handler_listener()
    {
        $this->shouldImplement('Netzmacht\Workflow\Handler\Listener');
    }

    function it_dispatches_build_form_event_during_validate(
        Form $form,
        Workflow $workflow,
        Context $context,
        Item $item,
        SymfonyEventDispatcher $eventDispatcher
    ) {
        $eventDispatcher->dispatch(
            BuildFormEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\BuildFormEvent')
        )
            ->shouldBeCalled();

        $this->onBuildForm($form, $workflow, $item, $context, static::TRANSITION_NAME);
    }

    function it_dispatches_validate_event_during_validate(
        Form $form,
        Workflow $workflow,
        Context $context,
        Item $item,
        SymfonyEventDispatcher $eventDispatcher
    ) {
        $eventDispatcher->dispatch(
            ValidateTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent')
        )
            ->shouldBeCalled();

        $this->onValidate($form, true, $workflow, $item, $context, static::TRANSITION_NAME);
    }

    function it_dispatches_pre_transition_event(
        Workflow $workflow,
        Context $context,
        Item $item,
        SymfonyEventDispatcher $eventDispatcher
    ) {
        $eventDispatcher->dispatch(
            PreTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\PreTransitionEvent')
        )
            ->shouldBeCalled();

        $this->onPreTransit($workflow, $item, $context, static::TRANSITION_NAME);
    }

    function it_dispatches_post_transition_event(
        Workflow $workflow,
        Context $context,
        Item $item,
        State $state,
        SymfonyEventDispatcher $eventDispatcher
    ) {
        $eventDispatcher->dispatch(
            PostTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\PostTransitionEvent')
        )
            ->shouldBeCalled();

        $this->onPostTransit($workflow, $item, $context, $state);
    }
}
