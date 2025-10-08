<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Context;
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

    public function let(Workflow $workflow): void
    {
        $this->entityId = EntityId::fromProviderNameAndId('entity', 4);

        $workflow->addTransition(Argument::type(Transition::class))->willReturn($workflow);

        $this->beConstructedThrough('initialize', [$this->entityId, self::$entity]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Item');
    }

    public function it_restores_state_history(State $state): void
    {
        $this->beConstructedThrough('reconstitute', [$this->entityId, self::$entity, [$state]]);
    }

    public function it_has_an_entity_id(): void
    {
        $this->getEntityId()->shouldReturn($this->entityId);
    }

    public function it_has_an_entity(): void
    {
        $this->getEntity()->shouldReturn(self::$entity);
    }

    public function it_knows_if_workflow_is_started(): void
    {
        $this->isWorkflowStarted()->shouldReturn(false);
    }

    public function it_transits_to_a_successful_state(
        State $state,
        State $newState,
        Workflow $workflow,
    ): void {
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->willReturn('workflow_name');
        $state->isSuccessful()->willReturn(true);
        $state->transit(Argument::cetera())->willReturn($newState);

        $newState->getWorkflowName()->willReturn('workflow_name');
        $newState->getStepName()->willReturn('target');
        $newState->isSuccessful()->willReturn(true);

        $this->it_restores_state_history($state);

        $transition = new Transition('transition_name', $workflow->getWrappedObject(), new Step('test'));

        $this->transit($transition, new Context(), true);

        $this->getCurrentStepName()->shouldReturn('target');
        $this->getWorkflowName()->shouldReturn('workflow_name');
        $this->getStateHistory()->shouldReturn([$state, $newState]);

        $this->getLatestSuccessfulState()->shouldHaveType(State::class);
        $this->getLatestSuccessfulState()->shouldNotBe($state);
    }

    public function it_starts_a_new_workflow_state(
        Workflow $workflow,
    ): void {
        $workflow->getName()->willReturn('workflow');

        $step = new Step('step', workflowName: 'workflow');

        $transition = new Transition('transition_name', $workflow->getWrappedObject(), $step);

        $this->beConstructedThrough('initialize', [$this->entityId, self::$entity]);
        $this->start($transition, new Context(), true)->shouldHaveType(State::class);
    }

    public function it_gets_last_successful_state(State $state, State $failedState): void
    {
        $failedState->isSuccessful()->willReturn(false);
        $failedState->getStepName()->willReturn('failed');

        $state->isSuccessful()->willReturn(true);
        $state->getStepName()->willReturn('start');
        $state->getWorkflowName()->shouldBeCalled();

        $this->beConstructedThrough('reconstitute', [$this->entityId, self::$entity, [$state, $failedState]]);

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

        $this->beConstructedThrough('reconstitute', [$this->entityId, self::$entity, [$state, $failedState]]);

        $this->getLatestSuccessfulState()->shouldReturn($state);
        $this->getLatestStateOccurred()->shouldReturn($failedState);
    }

    public function it_records_state_changes_and_release_them(
        Workflow $workflow,
    ): void {
        $context    = new Context();
        $stepTo     = new Step('step_to', workflowName: 'workflow');
        $transition = new Transition('transition', $workflow->getWrappedObject(), $stepTo);

        $workflow->getName()
            ->willReturn('workflow');

        $this->start($transition, $context, true);
        $this->transit($transition, $context, true);

        $this->releaseRecordedStateChanges()->shouldHaveCount(2);
        $this->releaseRecordedStateChanges()->shouldHaveCount(0);
    }
}
