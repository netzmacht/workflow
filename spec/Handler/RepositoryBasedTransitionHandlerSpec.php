<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Action;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class RepositoryBasedTransitionHandlerSpec extends ObjectBehavior
{
    public const string TRANSITION_NAME = 'transition_name';

    public const string STEP_NAME     = 'step_name';
    public const string WORKFLOW_NAME = 'workflow_name';

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    private EntityId $entityId;

    private Transition $transition;

    private Step $step;

    public function let(
        Item $item,
        Workflow $workflow,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
        State $state,
    ): void {
        $this->entityId = EntityId::fromProviderNameAndId('entity', '2');

        $this->step = new Step(self::STEP_NAME);
        $this->step->allowTransition(self::TRANSITION_NAME);

        $workflow->addTransition(Argument::type(Transition::class))->willReturn($workflow);
        $this->transition = new Transition(
            self::TRANSITION_NAME,
            $workflow->getWrappedObject(),
            $this->step,
        );

        $workflow->getStep(self::STEP_NAME)->willReturn($this->step);
        $workflow->getStartTransition()->willReturn($this->transition);
        $workflow->getName()->willReturn(self::WORKFLOW_NAME);

        $workflow->getTransition(self::TRANSITION_NAME)->willReturn($this->transition);

        $item->transit($this->transition, Argument::type(Context::class))
            ->willReturn($state);

        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(self::STEP_NAME);
        $item->getEntity()->willReturn(self::$entity);

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
        $this->shouldHaveType(RepositoryBasedTransitionHandler::class);
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
        $this->getTransition()->shouldReturn($this->transition);
    }

    public function it_gets_transition_if_already_started(Item $item): void
    {
        $item->isWorkflowStarted()->willReturn(true);

        $this->getTransition()->shouldReturn($this->transition);
    }

    public function it_gets_item(Item $item): void
    {
        $this->getItem()->shouldReturn($item);
    }

    public function it_gets_current_step_for_started_workflow(Item $item, Workflow $workflow): void
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(self::STEP_NAME);

        $workflow->getStep('start')->willReturn($this->step);

        $this->getCurrentStep()->shouldReturn($this->step);
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

    public function it_checks_if_input_data_is_required(): void
    {
        $this->transition->addAction($this->actionWithRequiredPayload(['foo']));

        $this->getRequiredPayloadProperties()->shouldReturn(['foo']);
    }

    public function it_checks_if_input_data_is_not_required(): void
    {
        $this->getRequiredPayloadProperties()->shouldReturn([]);
    }

    public function it_gets_the_context(): void
    {
        $this->getContext()->shouldHaveType(Context::class);
    }

    public function it_validates(Workflow $workflow): void
    {
        $this->transition = new Transition(
            self::TRANSITION_NAME,
            $workflow->getWrappedObject(),
            $this->step,
        );

        $this->transition->addAction($this->actionWithRequiredPayload(['foo']));

        $this->validate([])->shouldReturn(true);
    }

    public function it_throws_during_transits_if_not_validated(): void
    {
        $this->shouldThrow(WorkflowException::class)->duringTransit();
    }

    public function it_transits_to_next_state(Item $item, State $state, State $newState): void
    {
        $item->getLatestStateOccurred()->willReturn($state);
        $item->releaseRecordedStateChanges()
            ->shouldBeCalledOnce()
            ->willReturn([$newState]);

        $item->transit($this->transition, Argument::type(Context::class), true)->willReturn($newState);

        $this->validate([]);
        $this->transit()->shouldHaveType(State::class);
    }

    public function it_checks_if_transition_is_available(): void
    {
        $this->isAvailable()->shouldReturn(true);
    }

    /**
     * @param list<string> $properties
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function actionWithRequiredPayload(array $properties): Action
    {
        return new class ($properties) implements Action
        {
            /** @param list<string> $properties */
            public function __construct(private readonly array $properties)
            {
            }

            /** {@inheritDoc} */
            public function getRequiredPayloadProperties(Item $item): array
            {
                return $this->properties;
            }

            public function validate(Item $item, Context $context): bool
            {
                return true;
            }

            public function transit(Transition $transition, Item $item, Context $context): void
            {
            }
        };
    }
}
