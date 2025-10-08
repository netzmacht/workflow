<?php

declare(strict_types=1);

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Base;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Exception\TransitionNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;

final class WorkflowSpec extends ObjectBehavior
{
    public const string NAME       = 'workflow';
    public const string PROVIDER   = 'provider_name';
    public const string START_STEP = 'start_step';

    /** @var array<string, mixed> */
    protected static array $entity = ['id' => 5];

    private Transition $transition;

    private Step $step;

    public function let(): void
    {
        $this->beConstructedWith(self::NAME, self::PROVIDER);

        $this->step       = new Step(self::START_STEP);
        $this->transition = new Transition('start', $this->getWrappedObject(), $this->step);

        $this->addStep($this->step);
        $this->addTransition($this->transition);
        $this->setStartTransition('start');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Workflow::class);
    }

    public function it_behaves_like_base(): void
    {
        $this->shouldHaveType(Base::class);
    }

    public function it_adds_a_step(): void
    {
        $anotherStep = new Step('another');

        $this->addStep($anotherStep)->shouldReturn($this);
        $this->getStep('another')->shouldReturn($anotherStep);
    }

    public function it_throws_if_step_not_exists(): void
    {
        $this->shouldThrow('Netzmacht\Workflow\Flow\Exception\StepNotFoundException')->duringGetStep('not_set');
    }

    public function it_adds_a_transition(): void
    {
        $transition = new Transition('another', $this->getWrappedObject(), new Step('target'));

        $this->addTransition($transition)->shouldReturn($this);
        $this->getTransition('another')->shouldReturn($transition);
    }

    public function it_throws_if_transition_not_exists(): void
    {
        $this
            ->shouldThrow(TransitionNotFound::class)
            ->duringGetTransition('not_set');
    }

    public function it_has_a_start_transition(): void
    {
        $this->setStartTransition('start')->shouldReturn($this);
        $this->getStartTransition()->shouldReturn($this->transition);
    }

    public function it_throws_if_start_transition_is_not_part_of_workflow(): void
    {
        $this
            ->shouldThrow(TransitionNotFound::class)
            ->duringSetStartTransition('not_set');
    }

    public function it_knows_if_transition_exists(): void
    {
        $this->hasTransition('start')->shouldReturn(true);
        $this->hasTransition('test')->shouldReturn(false);
    }

    public function it_gets_all_transitions(): void
    {
        $this->getTransitions()->shouldReturn([$this->transition]);
    }

    public function it_knows_if_start_transition_is_available_for_an_item(Item $item): void
    {
        $item->isWorkflowStarted()->willReturn(false);

        $this->isTransitionAvailable($item, new Context(), 'start')->shouldReturn(true);
    }

    public function it_knows_if_start_transition_is_not_available_for_an_item(Item $item): void
    {
        $item->isWorkflowStarted()->willReturn(false);

        $this->isTransitionAvailable($item, new Context(), 'start2')->shouldReturn(false);
    }

    public function it_knows_if_transition_is_not_available_for_an_item(Item $item): void
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('started');

        $step = new Step('started');
        $this->addStep($step);

        $this->isTransitionAvailable($item, new Context(), 'start')->shouldReturn(false);
    }

    public function it_knows_if_transition_is_available_for_an_item(Item $item): void
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('started');

        $context = new Context();

        $step = new Step('started');
        $step->allowTransition('next');
        $this->addStep($step);

        $transition = new Transition('next', $this->getWrappedObject());
        $this->addTransition($transition);

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
