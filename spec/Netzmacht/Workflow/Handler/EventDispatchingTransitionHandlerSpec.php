<?php

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\Entity;
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
use Netzmacht\Workflow\Handler\Event\BuildFormEvent;
use Netzmacht\Workflow\Handler\Event\PostTransitionEvent;
use Netzmacht\Workflow\Handler\Event\PreTransitionEvent;
use Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent;
use Netzmacht\Workflow\Handler\EventDispatchingTransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class TransitionHandlerSpec
 * @package spec\Netzmacht\Contao\Workflow
 * @mixin EventDispatchingTransitionHandler
 */
class EventDispatchingTransitionHandlerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';
    const CONTEXT_CLASS = 'Netzmacht\Workflow\Flow\Context';
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';
    const WORKFLOW_NAME = 'workflow_name';
    const STEP_NAME = 'step_name';

    function let(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityRepository $entityRepository,
        TransactionHandler $transactionHandler,
        EventDispatcher $eventDispatcher,
        Transition $transition,
        State $state,
        Entity $entity,
        EntityId $entityId,
        Step $step
    )  {
        $workflow->getStep(static::STEP_NAME)->willReturn($step);
        $workflow->getStartTransition()->willReturn($transition);
        $workflow->getName()->willReturn(static::WORKFLOW_NAME);

        $step->isTransitionAllowed(static::TRANSITION_NAME)->willReturn(true);
        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->requiresInputData()->willReturn(false);
        $transition->transit(
            $item,
            Argument::type(static::CONTEXT_CLASS),
            Argument::type(static::ERROR_COLLECTION_CLASS)
        )->willReturn($state);

        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(static::STEP_NAME);
        $item->getEntity()->willReturn($entity);

        $entity->getEntityId()->willReturn($entityId);
        $entityId->__toString()->willReturn('entity::2');

        $this->beConstructedWith(
            $item,
            $workflow,
            static::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $eventDispatcher
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\EventDispatchingTransitionHandler');
    }

    function it_dispatches_build_form_event_during_validate(
        Form $form,
        Transition $transition,
        Item $item,
        EventDispatcher $eventDispatcher
    ) {
        $transition->buildForm($form, $item)->shouldBeCalled();

        $this->validate($form);

        $eventDispatcher->dispatch(
            BuildFormEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\BuildFormEvent')
        )
            ->shouldHaveBeenCalled();
    }

    function it_dispatches_validate_event_during_validate(
        Form $form,
        Transition $transition,
        Item $item,
        EventDispatcher $eventDispatcher
    ) {
        $transition->buildForm($form, $item)->shouldBeCalled();
        $this->validate($form);

        $eventDispatcher->dispatch(
            ValidateTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent')
        )
            ->shouldHaveBeenCalled();
    }

    function it_dispatches_pre_transition_event(
        Form $form,
        Transition $transition,
        Item $item,
        EventDispatcher $eventDispatcher
    ) {
        $transition->buildForm($form, $item)->shouldBeCalled();
        $this->validate($form);
        $this->transit();

        $eventDispatcher->dispatch(
            PreTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\PreTransitionEvent')
        )
            ->shouldHaveBeenCalled();
    }

    function it_dispatches_post_transition_event(
        Form $form,
        Transition $transition,
        Item $item,
        EventDispatcher $eventDispatcher
    ) {
        $transition->buildForm($form, $item)->shouldBeCalled();
        $this->validate($form);
        $this->transit();

        $eventDispatcher->dispatch(
            PostTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\PostTransitionEvent')
        )
            ->shouldHaveBeenCalled();
    }
}
