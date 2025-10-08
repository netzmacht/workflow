<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

final class WorkflowSpec extends ObjectBehavior
{
    public const string NAME       = 'workflow';
    public const string PROVIDER   = 'provider_name';
    public const string START_STEP = 'start_step';

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    public function let(Step $transitionStep, Transition $transition): void
    {
        $transitionStep->getName()->willReturn(self::START_STEP);

        $transition->getName()->willReturn('start');

        $this->beConstructedWith(self::NAME, self::PROVIDER);

        $this->addStep($transitionStep);
        $this->addTransition($transition, true);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Workflow');
    }

    public function it_behaves_like_base(): void
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Base');
    }

    public function it_adds_a_step(Step $anotherStep): void
    {
        $anotherStep->getName()->willReturn('another');

        $this->addStep($anotherStep)->shouldReturn($this);
        $this->getStep('another')->shouldReturn($anotherStep);
    }

    public function it_throws_if_step_not_exists(): void
    {
        $this->shouldThrow('Netzmacht\Workflow\Flow\Exception\StepNotFoundException')->duringGetStep('not_set');
    }

    public function it_adds_a_transition(Transition $anotherTransition): void
    {
        $anotherTransition->getName()->willReturn('another');

        $this->addTransition($anotherTransition)->shouldReturn($this);
        $this->getTransition('another')->shouldReturn($anotherTransition);
    }

    public function it_throws_if_transition_not_exists(): void
    {
        $this
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\TransitionNotFound')
            ->duringGetTransition('not_set');
    }

    public function it_has_a_start_transition(Transition $transition): void
    {
        $this->setStartTransition('start')->shouldReturn($this);
        $this->getStartTransition()->shouldReturn($transition);
    }

    public function it_throws_if_start_transition_is_not_part_of_workflow(): void
    {
        $this
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\TransitionNotFound')
            ->duringSetStartTransition('not_set');
    }

    public function it_knows_if_transition_exists(): void
    {
        $this->hasTransition('start')->shouldReturn(true);
        $this->hasTransition('test')->shouldReturn(false);
    }

    public function it_gets_all_transitions(Transition $transition): void
    {
        $this->getTransitions()->shouldReturn([$transition]);
    }

    public function it_knows_if_start_transition_is_available_for_an_item(
        Item $item,
        Context $context,
    ): void {
        $item->isWorkflowStarted()->willReturn(false);

        $this->isTransitionAvailable($item, $context, 'start')->shouldReturn(true);
    }

    public function it_knows_if_start_transition_is_not_available_for_an_item(Item $item, Context $context): void
    {
        $item->isWorkflowStarted()->willReturn(false);

        $this->isTransitionAvailable($item, $context, 'start2')->shouldReturn(false);
    }

    public function it_knows_if_transition_is_not_available_for_an_item(
        Item $item,
        Step $step,
        Context $context,
    ): void {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('started');

        $step->getName()->willReturn('started');
        $step->isTransitionAllowed('start')->willReturn(false);
        $this->addStep($step);

        $this->isTransitionAvailable($item, $context, 'start')->shouldReturn(false);
    }

    public function it_knows_if_transition_is_available_for_an_item(
        Item $item,
        Step $step,
        Context $context,
        Transition $transition,
    ): void {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('started');

        $transition->getName()->willReturn('next');
        $transition->isAvailable($item, $context)->shouldBeCalled()->willReturn(true);
        $this->addTransition($transition);

        $step->getName()->willReturn('started');
        $step->isTransitionAllowed('next')->willReturn(true);
        $this->addStep($step);

        $this->isTransitionAvailable($item, $context, 'next')->shouldReturn(true);
    }

    public function it_can_be_limited_by_conditions(Condition $condition): void
    {
        $this->getCondition()->shouldBe(null);

        $this->addCondition($condition)->shouldReturn($this);

        $this->getCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition');
        $this->getCondition()->getConditions()->shouldReturn([$condition]);
    }

    public function it_is_limited_to_an_provider_name(): void
    {
        $this->getProviderName()->shouldReturn(self::PROVIDER);
    }

    public function it_matches_if_no_condition_is_set(): void
    {
        $entityId = EntityId::fromProviderNameAndId('entity', 2);

        $this->supports($entityId, static::$entity)->shouldReturn(true);
    }

    public function it_matches_if_condition_does(Condition $condition): void
    {
        $entityId = EntityId::fromProviderNameAndId('entity', 2);
        $condition->match($this, $entityId, static::$entity)->willReturn(true);

        $this->addCondition($condition);
        $this->supports($entityId, static::$entity)->shouldReturn(true);
    }

    public function it_does_not_match_if_condition_does_not(Condition $condition): void
    {
        $entityId = EntityId::fromProviderNameAndId('entity', 2);

        $condition->match($this, $entityId, static::$entity)->willReturn(false);

        $this->addCondition($condition);
        $this->supports($entityId, static::$entity)->shouldReturn(false);
    }
}
