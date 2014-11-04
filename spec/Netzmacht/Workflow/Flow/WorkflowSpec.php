<?php

namespace spec\Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Flow\Condition\Workflow\Condition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Step;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class WorkflowSpec
 * @package spec\Netzmacht\Workflow\Flow
 * @mixin \Netzmacht\Workflow\Flow\Workflow
 */
class WorkflowSpec extends ObjectBehavior
{
    const NAME = 'workflow';
    const PROVIDER = 'provider_name';
    const START_STEP = 'start_step';

    function let(Step $transitionStep, Transition $transition)
    {
        $transitionStep->getName()->willReturn(static::START_STEP);

        $transition->getName()->willReturn('start');
        $transition->setStepTo($transitionStep);
        $transition->setWorkflow(Argument::type('Netzmacht\Workflow\Flow\Workflow'))->shouldBeCalled();

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
        $this->shouldHaveType('Netzmacht\Workflow\Base');
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
        $anotherTransition->setWorkflow($this)->shouldBeCalled();

        $this->addTransition($anotherTransition)->shouldReturn($this);
        $this->getTransition('another')->shouldReturn($anotherTransition);
    }

    function it_throws_if_transition_not_exists()
    {
        $this
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException')
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
            ->shouldThrow('Netzmacht\Workflow\Flow\Exception\TransitionNotFoundException')
            ->duringSetStartTransition('not_set');
    }

    function it_adds_a_role(Role $role)
    {
        $role->getName()->willReturn('acl');

        $this->addRole($role)->shouldReturn($this);
        $this->getRole('acl')->shouldReturn($role);
        $this->getRoles()->shouldReturn(array($role));
    }

    function it_can_be_limited_by_conditions(Condition $condition)
    {
        $this->getCondition()->shouldBe(null);

        $this->addCondition($condition)->shouldReturn($this);

        $this->getCondition()->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Workflow\AndCondition');
        $this->getCondition()->getConditions()->shouldReturn(array($condition));
    }

    function it_is_limited_to_an_provider_name()
    {
        $this->getProviderName()->shouldReturn(static::PROVIDER);
    }

    function it_matches_if_no_condition_is_set(Entity $entity)
    {
        $this->match($entity)->shouldReturn(true);
    }

    function it_matches_if_condition_does(Condition $condition, Entity $entity)
    {
        $condition->match($this, $entity)->willReturn(true);

        $this->addCondition($condition);
        $this->match($entity)->shouldReturn(true);
    }

    function it_does_not_match_if_condition_does_not(Condition $condition, Entity $entity)
    {
        $condition->match($this, $entity)->willReturn(false);

        $this->addCondition($condition);
        $this->match($entity)->shouldReturn(false);
    }

    function it_starts_a_workflow(Item $item, Context $context, Transition $transition, State $state)
    {
        $transition->start($item, $context)->willReturn($state);

        $this->start($item, $context)->shouldReturn($state);
    }

    function it_transits_to_a_new_step(
        Item $item,
        Context $context,
        State $state,
        \Netzmacht\Workflow\Flow\Transition $anotherTransition,
        Step $transitionStep
    ) {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn(static::START_STEP);

        $anotherTransition->getName()->willReturn('another');
        $anotherTransition->setWorkflow($this)->shouldBeCalled();

        $this->addTransition($anotherTransition);

        $transitionStep->isTransitionAllowed('another')->willReturn(true);

        $anotherTransition->transit($item, $context)->willReturn($state);

        $this->transit($item, 'another', $context)->shouldReturn($state);
    }
}
