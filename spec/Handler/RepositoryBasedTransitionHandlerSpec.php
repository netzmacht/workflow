<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RepositoryBasedTransitionHandlerSpec extends ObjectBehavior
{
    public const string TRANSITION_NAME = 'transition_name';

    public const string STEP_NAME     = 'step_name';
    public const string WORKFLOW_NAME = 'workflow_name';

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    private EntityId $entityId;

    public function let(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Step $step,
        Transition $transition,
        State $state,
    ): void {
        $this->entityId = EntityId::fromProviderNameAndId('entity', '2');

        $workflow->getStep(self::STEP_NAME)->willReturn($step);
        $workflow->getStartTransition()->willReturn($transition);
        $workflow->getName()->willReturn(self::WORKFLOW_NAME);

        $step->isTransitionAllowed(self::TRANSITION_NAME)->willReturn(true);
        $workflow->getTransition(self::TRANSITION_NAME)->willReturn($transition);

        $transition->getName()->willReturn(self::TRANSITION_NAME);
        $transition->getRequiredPayloadProperties($item)->willReturn([]);

        $item->transit($transition, Argument::type(Context::class))
            ->willReturn($state);

        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(self::STEP_NAME);
        $item->getEntity()->willReturn(static::$entity);

        $this->beConstructedWith(
            $item,
            $workflow,
            self::TRANSITION_NAME,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler');
    }

    public function it_gets_workflow(Workflow $workflow): void
    {
        $this->getWorkflow()->shouldReturn($workflow);
    }

    public function it_gets_start_transition_if_not_started(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        Transition $transition,
    ): void {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
        );

        $item->isWorkflowStarted()->willReturn(false);
        $item->getEntityId()->willReturn($this->entityId);

        $workflow->getStartTransition()->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);
    }

    public function it_gets_transition_if_already_started(Item $item, Workflow $workflow, Transition $transition): void
    {
        $item->isWorkflowStarted()->willReturn(true);

        $workflow->getTransition(self::TRANSITION_NAME)->willReturn($transition);

        $this->getTransition()->shouldReturn($transition);
    }

    public function it_gets_item(Item $item): void
    {
        $this->getItem()->shouldReturn($item);
    }

    public function it_gets_current_step_for_started_workflow(Item $item, Workflow $workflow, Step $step): void
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');

        $workflow->getStep('start')->willReturn($step);

        $this->getCurrentStep()->shouldReturn($step);
    }

    public function it_gets_null_instead_of_step_if_not_started(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
    ): void {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
        );

        $item->isWorkflowStarted()->willReturn(false);

        $this->getCurrentStep()->shouldBeNull();
    }

    public function it_checks_if_workflow_is_started(Item $item): void
    {
        $item->isWorkflowStarted()->willReturn(true);
        $this->isWorkflowStarted()->shouldReturn(true);
    }

    public function it_checks_if_workflow_is_not_started(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
    ): void {
        $this->beConstructedWith(
            $item,
            $workflow,
            null,
            $entityRepository,
            $stateRepository,
            $transactionHandler,
        );

        $item->isWorkflowStarted()->willReturn(false);
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    public function it_checks_if_input_data_is_required(Workflow $workflow, Transition $transition, Item $item): void
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getRequiredPayloadProperties($item)->willReturn(['foo']);

        $this->getRequiredPayloadProperties()->shouldReturn(['foo']);
    }

    public function it_checks_if_input_data_is_not_required(
        Workflow $workflow,
        Transition $transition,
        Item $item,
    ): void {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getRequiredPayloadProperties($item)->willReturn([]);

        $this->getRequiredPayloadProperties()->shouldReturn([]);
    }

    public function it_gets_the_context(): void
    {
        $this->getContext()->shouldHaveType(Context::class);
    }

    public function it_validates(Workflow $workflow, Transition $transition, Item $item): void
    {
        $workflow->getStartTransition()->willReturn($transition);
        $transition->getName()->willReturn(self::TRANSITION_NAME);

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

    public function it_throws_during_transits_if_not_validated(Workflow $workflow, Transition $transition): void
    {
        $workflow->getStartTransition()->willReturn($transition);

        $this->shouldThrow('Netzmacht\Workflow\Exception\WorkflowException')->duringTransit();
    }

    public function it_transits_to_next_state(Transition $transition, Item $item, State $state): void
    {
        $item->releaseRecordedStateChanges()
            ->shouldBeCalledOnce()
            ->willReturn([$state]);

        $transition->validate($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->execute($item, Argument::type(Context::class))
            ->willReturn($state)
            ->shouldBeCalledOnce();

        $transition->checkCondition($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $transition->checkPreCondition($item, Argument::type(Context::class))
            ->willReturn(true)
            ->shouldBeCalled();

        $this->validate([]);
        $this->transit()->shouldHaveType(State::class);
    }

    public function it_checks_if_transition_is_available(Transition $transition, Item $item): void
    {
        $transition->getName()->willReturn(self::TRANSITION_NAME);
        $transition->isAvailable(
            $item,
            Argument::type(Context::class),
        )->willReturn(true);

        $this->isAvailable()->shouldReturn(true);
    }
}
