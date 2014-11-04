<?php

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
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

    function let(
        Item $item,
        Workflow $workflow,
        StateRepository $stateRepository,
        EntityRepository $entityRepository,
        TransactionHandler $transactionHandler,
        Context $context,
        EventDispatcher $eventDispatcher,
        Transition $transition,
        State $state,
        Entity $entity
    )  {
        $this->beConstructedWith(
            $item,
            $workflow,
            static::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $context,
            $eventDispatcher
        );

        $workflow->getStartTransition()->willReturn($transition);
        $workflow->start($item, $context)->willReturn($state);

        $item->getEntity()->willReturn($entity);
        $item->isWorkflowStarted()->willReturn(false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\EventDispatchingTransitionHandler');
    }

    function it_dispatches_build_form_event_during_validate(Form $form, EventDispatcher $eventDispatcher)
    {
        $this->validate($form);

        $eventDispatcher->dispatch(
            BuildFormEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\BuildFormEvent')
        )
            ->shouldHaveBeenCalled();
    }

    function it_dispatches_validate_event_during_validate(Form $form, EventDispatcher $eventDispatcher)
    {
        $this->validate($form);

        $eventDispatcher->dispatch(
            ValidateTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\ValidateTransitionEvent')
        )
            ->shouldHaveBeenCalled();
    }

    function it_dispatches_pre_transition_event(Form $form, EventDispatcher $eventDispatcher)
    {
        $this->validate($form);
        $this->transit();

        $eventDispatcher->dispatch(
            PreTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\PreTransitionEvent')
        )
            ->shouldHaveBeenCalled();
    }

    function it_dispatches_post_transition_event(Form $form, EventDispatcher $eventDispatcher)
    {
        $this->validate($form);
        $this->transit();

        $eventDispatcher->dispatch(
            PostTransitionEvent::NAME,
            Argument::type('Netzmacht\Workflow\Handler\Event\PostTransitionEvent')
        )
            ->shouldHaveBeenCalled();
    }
}
