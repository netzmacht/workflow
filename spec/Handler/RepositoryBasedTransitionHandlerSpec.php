<?php

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RepositoryBasedTransitionHandlerSpec
 * @package spec\Netzmacht\Workflow\Handler
 */
class RepositoryBasedTransitionHandlerSpec extends ObjectBehavior
{
    const TRANSITION_NAME = 'transition_name';
    
    const STEP_NAME = 'step_name';
    const WORKFLOW_NAME = 'workflow_name';

    protected static $entity = array('id' => 5);

    /**
     * @var EntityId
     */
    private $entityId;

    function let(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Step $step,
        Transition $transition,
        State $state
    ) {
        $this->entityId = EntityId::fromProviderNameAndId('entity', '2');

        $workflow->getStep(static::STEP_NAME)->willReturn($step);
        $workflow->getStartTransition()->willReturn($transition);
        $workflow->getName()->willReturn(static::WORKFLOW_NAME);

        $step->isTransitionAllowed(static::TRANSITION_NAME)->willReturn(true);
        $workflow->getTransition(static::TRANSITION_NAME)->willReturn($transition);

        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->getRequiredPayloadProperties($item)->willReturn([]);

        $item->transit($transition, Argument::type(Context::class))
            ->willReturn($state);

        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(static::STEP_NAME);
        $item->getEntity()->willReturn(static::$entity);

        $this->beConstructedWith(
            $item,
            $workflow,
            static::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler
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
        Transition $transition
    ) {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler
        );

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntityId()->willReturn($this->entityId);

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
        TransactionHandler $transactionHandler
    ) {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler
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
        TransactionHandler $transactionHandler
    ) {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler
        );

        $item->isWorkflowStarted()->willReturn(false);
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    function it_checks_if_input_data_is_required(Workflow $workflow, Transition $transition, Item $item)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getRequiredPayloadProperties($item)->willReturn(['foo']);

        $this->getRequiredPayloadProperties()->shouldReturn(['foo']);
    }
    function it_checks_if_input_data_is_not_required(Workflow $workflow, Transition $transition, Item $item)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getRequiredPayloadProperties($item)->willReturn([]);

        $this->getRequiredPayloadProperties()->shouldReturn([]);
    }

    function it_gets_the_context()
    {
        $this->getContext()->shouldHaveType(Context::class);
    }

    function it_validates(Workflow $workflow, Transition $transition, Item $item)
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getName()->willReturn(static::TRANSITION_NAME);

        $transition->getRequiredPayloadProperties($item)->willReturn(['foo']);

        $transition->validate($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->checkPreCondition($item, Argument::type(Context::class))
            ->shouldBeCalled()
            ->willReturn(true);

        $transition->checkCondition($item, Argument::type(Context::class))
            ->shouldBeCalled()
            ->willReturn(true);

        $this->validate([])->shouldReturn(true);
    }

    function it_throws_during_transits_if_not_validated(Workflow $workflow, Transition $transition)
    {
        $workflow->getStartTransition()->willReturn($transition);

        $this->shouldThrow('Netzmacht\Workflow\Exception\WorkflowException')->duringTransit();
    }

    function it_transits_to_next_state(Transition $transition, Item $item, State $state)
    {
        $item->transit($transition, Argument::type(Context::class), true)
            ->shouldBeCalled()
            ->willReturn($state);

        $transition->validate($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->executeActions($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->executePostActions($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->checkCondition($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->checkPreCondition($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $this->validate([]);
        $this->transit()->shouldHaveType('Netzmacht\Workflow\Flow\State');
    }

    function it_checks_if_transition_is_available(Transition $transition, Item $item)
    {
        $transition->getName()->willReturn(static::TRANSITION_NAME);
        $transition->isAvailable(
            $item,
            Argument::type(Context::class)
        )->willReturn(true);

        $this->isAvailable()->shouldReturn(true);
    }
}
