<?php

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Handler\Listener;
use Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RepositoryBasedTransitionHandlerSpec
 * @package spec\Netzmacht\Workflow\Handler
 * @mixin RepositoryBasedTransitionHandler
 */
class RepositoryBasedTransitionHandlerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';

    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';

    const CONTEXT_CLASS = 'Netzmacht\Workflow\Flow\Context';
    const STEP_NAME = 'step_name';
    const WORKFLOW_NAME = 'workflow_name';

    protected static $entity = array('id' => 5);

    function let(
        Item $item,
        EntityId $entityId,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Step $step,
        Transition $transition,
        State $state,
        Listener $listener
    ) {
        $workflow->getStep(static::STEP_NAME)->willReturn($step);
        $workflow->getStartTransition()->willReturn($transition);
        $workflow->getName()->willReturn(static::WORKFLOW_NAME);

        $step->isTransitionAllowed(static::TRANSITION_NAME)->willReturn(true);
        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->isInputRequired($item)->willReturn(false);
        $transition->transit(
            $item,
            Argument::type(static::CONTEXT_CLASS),
            Argument::type(static::ERROR_COLLECTION_CLASS)
        )->willReturn($state);

        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(static::STEP_NAME);
        $item->getEntity()->willReturn(static::$entity);

        $entityId->__toString()->willReturn('entity::2');

        $this->beConstructedWith(
            $item,
            $workflow,
            static::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $listener
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler');
    }

    function it_gets_workflow(Workflow $workflow)
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    function it_gets_start_transition_if_not_started(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Transition $transition,
        Listener $listener,
        EntityId $entityId
    ) {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $listener
        );

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntityId()->willReturn($entityId);

        $workflow->getStartTransition()->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);
    }

    function it_gets_transition_if_already_started(Item $item, Workflow $workflow, Transition $transition)
    {
        $item->isWorkflowStarted()->willReturn(true);

        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);

    }

    function it_gets_item(Item $item)
    {
        $this->getItem()->shouldReturn($item);
    }

    function it_gets_form_after_validation(Form $form, Workflow $workflow, Transition $transition, Item $item)
    {
        $transition->buildForm($form, $item)->shouldBeCalled();

        $workflow->getStartTransition()->willReturn($transition);

        $this->getForm()->shouldReturn(null);
        $this->validate($form);
        $this->getForm()->shouldReturn($form);
    }

    function it_gets_current_step_for_started_workflow(Item $item, Workflow $workflow, Step $step)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');

        $workflow->getStep('start')->willReturn($step);

        $this->getCurrentStep()->shouldReturn($step);
    }

    function it_gets_null_instead_of_step_if_not_started(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Listener $listener
    ) {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $listener
        );

        $item->isWorkflowStarted()->willReturn(false);

        $this->getCurrentStep()->shouldBeNull();
    }

    function it_checks_if_workflow_is_started(Item $item)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $this->isWorkflowStarted()->shouldReturn(true);
    }

    function it_checks_if_workflow_is_not_started(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Listener $listener
    ) {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
            $listener
        );

        $item->isWorkflowStarted()->willReturn(false);
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    function it_checks_if_input_data_is_required(Workflow $workflow, Transition $transition, Item $item)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->isInputRequired($item)->willReturn(true);

        $this->isInputRequired()->shouldReturn(true);
    }
    function it_checks_if_input_data_is_not_required(Workflow $workflow, Transition $transition, Item $item)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->isInputRequired($item)->willReturn(false);

        $this->isInputRequired()->shouldReturn(false);
    }

    function it_gets_the_context()
    {
        $this->getContext()->shouldHaveType(self::CONTEXT_CLASS);
    }

    function it_gets_the_error_collection()
    {
        $this->getErrorCollection()->shouldHaveType(self::ERROR_COLLECTION_CLASS);
    }

    function it_validates(Form $form, Workflow $workflow, Transition $transition, Item $item, Listener $listener)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->buildForm($form, $item)->shouldBeCalled();
        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->isInputRequired($item)->willReturn(true);

        $listener->onBuildForm(Argument::cetera())->shouldBeCalled();
        $listener->onValidate(Argument::cetera())->willReturn(true);
        $form->validate(Argument::type(self::CONTEXT_CLASS))->shouldBeCalled()->willReturn(true);

        $this->validate($form)->shouldReturn(true);
    }

    function it_throws_during_transits_if_not_validated(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);

        $this->shouldThrow('Netzmacht\Workflow\Flow\Exception\WorkflowException')->duringTransit();
    }

    function it_transits_to_next_state(
        Form $form, Transition $transition, Item $item, Listener $listener
    ) {
        $listener->onBuildForm(Argument::cetera())->shouldBeCalled();
        $listener->onValidate(Argument::cetera())->willReturn(true);
        $listener->onPreTransit(Argument::cetera())->shouldBeCalled();
        $listener->onPostTransit(Argument::cetera())->shouldBeCalled();

        $transition->buildForm($form, $item)->shouldBeCalled();

        $this->validate($form);
        $this->transit()->shouldHaveType('Netzmacht\Workflow\Flow\State');
    }
}
