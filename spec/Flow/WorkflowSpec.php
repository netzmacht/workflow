<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

/**
 * Class WorkflowSpec
 *
 * @package spec\Netzmacht\Workflow\Flow
 */
class WorkflowSpec extends ObjectBehavior
{
    const NAME = 'workflow';
    const PROVIDER = 'provider_name';
    const START_STEP = 'start_step';

    protected static $entity = ['id' => 5];

    function let(Step $transitionStep, Transition $transition)
    {
        $transitionStep->getName()->willReturn(static::START_STEP);

        $transition->getName()->willReturn('start');

        $this->beConstructedWith(static::NAME, static::PROVIDER);

        $this->addStep($transitionStep);
        $this->addTransition($transition, true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Workflow');
    }

    function it_behaves_like_base()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Base');
    }

    function it_adds_a_step(Step $anotherStep)
    {
        $anotherStep->getName()->willReturn('another');

        $this->addStep($anotherStep)->shouldReturn($this);
        $this->getStep('another')->shouldReturn($anotherStep);
    }

    function it_throws_if_step_not_exists()
    {
        $this->shouldThrow('Netzmacht\Workflow\Flow\Exception\StepNotFoundException')->duringGetStep('not_set');
    }

    function it_adds_a_transition(Transition $anotherTransition)
    {
        $anotherTransition->getName()->willReturn('another');

        $this->addTransition($anotherTransition)->shouldReturn($this);
        $this->getTransition('another')->shouldReturn($anotherTransition);
    }

    function it_throws_if_transition_not_exists()
    {
        $this
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\TransitionNotFound')
            ->duringGetTransition('not_set');
    }

    function it_has_a_start_transition(Transition $transition)
    {
        $this->setStartTransition('start')->shouldReturn($this);
        $this->getStartTransition()->shouldReturn($transition);
    }

    function it_throws_if_start_transition_is_not_part_of_workflow()
    {
        $this
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\TransitionNotFound')
            ->duringSetStartTransition('not_set');
    }

    function it_knows_if_transition_exists()
    {
        $this->hasTransition('start')->shouldReturn(true);
        $this->hasTransition('test')->shouldReturn(false);
    }

    function it_gets_all_transitions(Transition $transition)
    {
        $this->getTransitions()->shouldReturn([$transition]);
    }

    function it_knows_if_start_transition_is_available_for_an_item(
        Item $item,
        Context $context
    ) {
        $item->isWorkflowStarted()->willReturn(false);

        $this->isTransitionAvailable($item, $context, 'start')->shouldReturn(true);
    }

    function it_knows_if_start_transition_is_not_available_for_an_item(Item $item, Context $context)
    {
        $item->isWorkflowStarted()->willReturn(false);

        $this->isTransitionAvailable($item, $context, 'start2')->shouldReturn(false);
    }

    function it_knows_if_transition_is_not_available_for_an_item(
        Item $item,
        Step $step,
        Context $context
    ) {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('started');

        $step->getName()->willReturn('started');
        $step->isTransitionAllowed('start')->willReturn(false);
        $this->addStep($step);

        $this->isTransitionAvailable($item, $context, 'start')->shouldReturn(false);
    }

    function it_knows_if_transition_is_available_for_an_item(
        Item $item,
        Step $step,
        Context $context,
        Transition $transition
    ) {
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

    function it_can_be_limited_by_conditions(Condition $condition)
    {
        $this->getCondition()->shouldBe(null);

        $this->addCondition($condition)->shouldReturn($this);

        $this->getCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition');
        $this->getCondition()->getConditions()->shouldReturn([$condition]);
    }

    function it_is_limited_to_an_provider_name()
    {
        $this->getProviderName()->shouldReturn(static::PROVIDER);
    }

    function it_matches_if_no_condition_is_set()
    {
        $entityId = EntityId::fromProviderNameAndId('entity', 2);

        $this->supports($entityId, static::$entity)->shouldReturn(true);
    }

    function it_matches_if_condition_does(Condition $condition)
    {
        $entityId = EntityId::fromProviderNameAndId('entity', 2);
        $condition->match($this, $entityId, static::$entity)->willReturn(true);

        $this->addCondition($condition);
        $this->supports($entityId, static::$entity)->shouldReturn(true);
    }

    function it_does_not_match_if_condition_does_not(Condition $condition)
    {
        $entityId = EntityId::fromProviderNameAndId('entity', 2);

        $condition->match($this, $entityId, static::$entity)->willReturn(false);

        $this->addCondition($condition);
        $this->supports($entityId, static::$entity)->shouldReturn(false);
    }
}
