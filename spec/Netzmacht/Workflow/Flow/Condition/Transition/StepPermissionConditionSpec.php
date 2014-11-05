<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Acl\User;
use Netzmacht\Workflow\Flow\Condition\Transition\StepPermissionCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Step;
use Netzmacht\Workflow\Flow\Transition;
use Netzmacht\Workflow\Flow\Workflow;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class StepPermissionConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin StepPermissionCondition
 */
class StepPermissionConditionSpec extends ObjectBehavior
{
    function let(User $user, Transition $transition, Workflow $workflow, Step $step, Role $role)
    {
        $this->beConstructedWith($user);

        $transition->getWorkflow()->willReturn($workflow);
        $workflow->getStep('step')->willReturn($step);

        $step->getRole()->willReturn($role);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\StepPermissionCondition');
    }

    function it_is_a_transition_condition()
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Transition\Condition');
    }

    function it_matches_if_workflow_not_started_by_default(Transition $transition, Item $item, Context $context)
    {
        $item->isWorkflowStarted()->willReturn(false);

        $this->match($transition, $item, $context)->shouldReturn(true);
        $this->shouldNotHaveError();
    }

    function it_does_match_if_workflow_not_started_when_disabled(Transition $transition, Item $item, Context $context)
    {
        $transition->getName()->shouldBeCalled();

        $item->isWorkflowStarted()->willReturn(false);
        $this->disallowStartTransition()->shouldReturn($this);

        $this->match($transition, $item, $context)->shouldReturn(false);
        $this->getError()->shouldHaveCount(2);

        $this->allowStartTransition()->shouldReturn($this);
        $this->match($transition, $item, $context)->shouldReturn(true);
        $this->shouldNotHaveError();
    }

    function it_matches_if_step_role_is_granted(Transition $transition, Item $item, Context $context, User $user, Role $role)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('step');

        $user->isGranted($role)->willReturn(true);

        $this->match($transition, $item, $context)->shouldReturn(true);
    }

    function it_does_not_match_if_step_has_no_assigned_role(Transition $transition, Item $item, Context $context, Step $step)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('step');

        $step->getRole()->willReturn(null);

        $this->match($transition, $item, $context)->shouldReturn(false);
        $this->shouldHaveError();
    }

    function it_does_not_match_if_step_role_is_not_granted(Transition $transition, Item $item, Context $context, User $user, Role $role)
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('step');

        $user->isGranted($role)->willReturn(false);

        $this->match($transition, $item, $context)->shouldReturn(false);
        $this->shouldHaveError();
    }
}
