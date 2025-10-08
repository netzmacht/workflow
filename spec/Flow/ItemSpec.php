<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Context\Properties;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class ItemSpec extends ObjectBehavior
{
    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    private EntityId $entityId;

    public function let(): void
    {
        $this->entityId = EntityId::fromProviderNameAndId('entity', 4);

        $this->beConstructedThrough('initialize', [$this->entityId, static::$entity]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

    public function it_restores_state_history(State $state): void
    {
        $this->beConstructedThrough('reconstitute', [$this->entityId, static::$entity, [$state]]);
    }

    public function it_has_an_entity_id(): void
    {
        $this->getEntityId()->shouldReturn($this->entityId);
    }

    public function it_has_an_entity(): void
    {
        $this->getEntity()->shouldReturn(static::$entity);
    }

    public function it_knows_if_workflow_is_started(): void
    {
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    public function it_transits_to_a_successful_state(
        State $state,
        State $newState,
        Transition $transition,
        Context $context,
    ): void {
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->willReturn('workflow_name');
        $state->isSuccessful()->willReturn(true);
        $state->transit(Argument::cetera())->willReturn($newState);

        $newState->getWorkflowName()->willReturn('workflow_name');
        $newState->getStepName()->willReturn('target');
        $newState->isSuccessful()->willReturn(true);

        $this->it_restores_state_history($state);

        $this->transit($transition, $context, true);

        $this->getCurrentStepName()->shouldReturn('target');
        $this->getWorkflowName()->shouldReturn('workflow_name');
        $this->getStateHistory()->shouldReturn([$state, $newState]);

        $this->getLatestSuccessfulState()->shouldHaveType('Netzmacht\Workflow\Flow\State');
        $this->getLatestSuccessfulState()->shouldNotBe($state);
    }

    public function it_starts_a_new_workflow_state(
        Transition $transition,
        Workflow $workflow,
        Step $step,
        Context $context,
    ): void {
        $errorCollection = new ErrorCollection();
        $properties      = new Properties();

        $workflow->getName()->willReturn('workflow');
        $step->getName()->willReturn('step');
        $step->getWorkflowName()->willReturn('workflow');

        $transition->getWorkflow()->willReturn($workflow);
        $transition->getName()->willReturn('transition_name');
        $transition->getStepTo()->willReturn($step);

        $context->getProperties()->willReturn($properties);
        $context->getErrorCollection()->willReturn($errorCollection);

        $this->beConstructedThrough('initialize', [$this->entityId, static::$entity]);
        $this->start($transition, $context, true)->shouldHaveType('Netzmacht\Workflow\Flow\State');
    }

    public function it_gets_last_successful_state(State $state, State $failedState): void
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', [$this->entityId, static::$entity, [$state, $failedState]]);

        $this->getCurrentStepName()->shouldReturn('start');
        $this->getLatestSuccessfulState()->shouldReturn($state);
        $this->getLatestStateOccurred()->shouldReturn($failedState);
    }

    public function it_gets_latest_state_from_history(State $state, State $failedState): void
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', [$this->entityId, static::$entity, [$state, $failedState]]);

        $this->getLatestSuccessfulState()->shouldReturn($state);
        $this->getLatestStateOccurred()->shouldReturn($failedState);
    }

    public function it_records_state_changes_and_release_them(
        Transition $transition,
        Workflow $workflow,
        Step $stepTo,
        Context $context,
    ): void {
        $errorCollection = new ErrorCollection();
        $properties      = new Properties();
        $payload         = new Properties();

        $transition->getWorkflow()
            ->willReturn($workflow);

        $transition->getName()
            ->willReturn('transition');

        $transition->getStepTo()
            ->willReturn($stepTo);

        $workflow->getName()
            ->willReturn('workflow');

        $stepTo->getName()
            ->willReturn('step_to');

        $stepTo->getWorkflowName()
            ->willReturn('workflow');

        $context->getErrorCollection()
            ->willReturn($errorCollection);

        $context->getProperties()
            ->willReturn($properties);

        $context->getPayload()
            ->willReturn($payload);

        $this->start($transition, $context, true);
        $this->transit($transition, $context, true);

        $this->releaseRecordedStateChanges()->shouldHaveCount(2);
        $this->releaseRecordedStateChanges()->shouldHaveCount(0);
    }
}
