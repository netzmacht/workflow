<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Flow\Context\ErrorCollection;
use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Security\User;
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
 */
class StepPermissionConditionSpec extends ObjectBehavior
{
    function let(User $user, Transition $transition, Workflow $workflow, Step $step, Permission $permission)
    {
        $this->beConstructedWith($user);

        $transition->getWorkflow()->willReturn($workflow);
        $workflow->getStep('step')->willReturn($step);

        $step->getPermission()->willReturn($permission);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\StepPermissionCondition');
    }

    function it_is_a_transition_condition()
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Transition\Condition');
    }

    function it_matches_if_workflow_not_started_by_default(
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $item->isWorkflowStarted()->willReturn(false);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_does_match_if_workflow_not_started_is_explicit_allowed(
        User $user,
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $this->beConstructedWith($user, false, false);
        $item->isWorkflowStarted()->willReturn(false);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_matches_if_step_permission_equals(
        Transition $transition,
        Item $item,
        Context $context,
        User $user,
        Permission $permission,
        ErrorCollection $errorCollection
    )
    {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('step');

        $user->hasPermission($permission)->willReturn(true);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_does_not_match_if_step_has_no_assigned_role(
        Transition $transition,
        Item $item,
        Context $context,
        Step $step,
        ErrorCollection $errorCollection
    ) {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('step');

        $step->getPermission()->willReturn(null);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_does_not_match_if_step_role_is_not_granted(
        Transition $transition,
        Item $item,
        Context $context,
        User $user,
        Permission $permission,
        ErrorCollection $errorCollection
    ) {
        $item->isWorkflowStarted()->willReturn(true);
        $item->getCurrentStepName()->willReturn('step');

        $permission->__toString()->willReturn('workflow/permission');

        $user->hasPermission($permission)->willReturn(false);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }
}
