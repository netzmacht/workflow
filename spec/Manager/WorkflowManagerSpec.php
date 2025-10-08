<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandlerFactory;
use Netzmacht\Workflow\Manager\Manager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @extends ObjectBehavior<array-key, mixed> */
final class WorkflowManagerSpec extends ObjectBehavior
{
    public const string ENTITY_PROVIDER_NAME = 'provider_name';

    public const int ENTITY_ID = 5;

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Manager::class);
    }

    public function let(
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        Workflow $workflow,
    ): void {
        $workflow->getName()->willReturn('workflow_a');

        $this->beConstructedWith($handlerFactory, $stateRepository, [$workflow]);
    }

    public function it_gets_workflow(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $workflow->supports($entityId, self::$entity)->willReturn(true);

        $this->getWorkflow($entityId, self::$entity)->shouldReturn($workflow);
    }

    public function it_gets_workflow_by_item(Workflow $workflow, Item $item): void
    {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $item->getWorkflowName()->willReturn('workflow_a');
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(self::$entity);

        $workflow->supports($entityId, self::$entity)->willReturn(true);

        $this->getWorkflowByItem($item)->shouldReturn($workflow);
    }

    public function it_adds_workflow(Workflow $anotherWorkflow): void
    {
        $anotherWorkflow->getName()->willReturn('another');

        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflowByName('another')->shouldReturn($anotherWorkflow);
    }

    public function it_returns_false_if_no_supported_workflow_found(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $workflow->supports($entityId, self::$entity)->willReturn(false);
    }

    public function it_throws_workflow_not_found_when_specific_workflow_not_exists(): void
    {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $this->shouldThrow(WorkflowNotFound::class)
            ->during('getWorkflowByName', [$entityId, self::$entity]);
    }

    public function it_knows_if_matching_workflow_exists(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $workflow->supports($entityId, self::$entity)->willReturn(true);
        $this->hasWorkflow($entityId, self::$entity)->shouldReturn(true);
    }

    public function it_knows_if_no_matching_workflow_exists(Workflow $workflow): void
    {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $workflow->supports($entityId, self::$entity)->willReturn(false);
        $this->hasWorkflow($entityId, self::$entity)->shouldReturn(false);
    }

    public function it_adds_an_workflow(Workflow $anotherWorkflow): void
    {
        $this->getWorkflows()->shouldNotContain($anotherWorkflow);
        $this->addWorkflow($anotherWorkflow)->shouldReturn($this);
        $this->getWorkflows()->shouldContain($anotherWorkflow);
    }

    public function it_returns_false_if_no_matching_workflow_found(
        Workflow $workflow,
        Item $item,
    ): void {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(self::$entity);

        $workflow->supports($entityId, self::$entity)->willReturn(false);
        $this->handle($item)->shouldReturn(null);
    }

    public function it_creates_handler_for_start_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler,
    ): void {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $item->getWorkflowName()->willReturn('workflow_a');
        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(self::$entity);
        $item->isWorkflowStarted()->willReturn(false);

        $workflow->supports($entityId, self::$entity)->willReturn(true);

        $handlerFactory->createTransitionHandler(
            $item,
            $workflow,
            Argument::any(),
            self::ENTITY_PROVIDER_NAME,
            $stateRepository,
        )->willReturn($transitionHandler);

        $this->handle($item)->shouldReturn($transitionHandler);
    }

    public function it_creates_handler_for_ongoing_transition(
        Workflow $workflow,
        Item $item,
        TransitionHandlerFactory $handlerFactory,
        StateRepository $stateRepository,
        TransitionHandler $transitionHandler,
    ): void {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $step = new Step('start');
        $step->allowTransition('next');

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(self::$entity);
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');
        $item->getWorkflowName()->willReturn('workflow_a');

        $workflow->addTransition(Argument::type(Transition::class))->willReturn($workflow);
        $transition = new Transition('next', $workflow->getWrappedObject(), $step);

        $workflow->supports($entityId, self::$entity)->willReturn(true);
        $workflow->getStep('start')->willReturn($step);
        $workflow->getTransition('next')->willReturn($transition);
        $workflow->getName()->willReturn('workflow_a');

        $handlerFactory->createTransitionHandler(
            $item,
            $workflow,
            Argument::any(),
            self::ENTITY_PROVIDER_NAME,
            $stateRepository,
        )
            ->willReturn($transitionHandler);

        $this->handle($item, 'next')->shouldReturn($transitionHandler);
    }

    public function it_throws_than_matches_workflow_is_not_same_as_current(Workflow $workflow, Item $item): void
    {
        $step = new Step('start');
        $step->allowTransition('next');

        $workflow->addTransition(Argument::type(Transition::class))->willReturn($workflow);
        $transition = new Transition('next', $workflow->getWrappedObject());
        $entityId   = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $item->getEntityId()->willReturn($entityId);
        $item->getEntity()->willReturn(self::$entity);
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('start');
        $item->getWorkflowName()->willReturn('workflow_a');

        $workflow->supports($entityId, self::$entity)->willReturn(true);
        $workflow->getStep('start')->willReturn($step);
        $workflow->getTransition('next')->willReturn($transition);
        $workflow->getName()->willReturn('workflow_b');

        $this
            ->shouldThrow('Netzmacht\Workflow\Exception\WorkflowException')
            ->duringHandle($item, 'next');
    }

    public function it_creates_an_item(
        StateRepository $stateRepository,
        State $state,
    ): void {
        $entityId = EntityId::fromProviderNameAndId(self::ENTITY_PROVIDER_NAME, self::ENTITY_ID);

        $state->getStepName()->willReturn('step');
        $state->getWorkflowName()->willReturn('workflow');
        $state->isSuccessful()->willReturn(true);

        $stateRepository->find($entityId)->willReturn([$state]);

        $this->createItem($entityId, self::$entity)->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }
}
