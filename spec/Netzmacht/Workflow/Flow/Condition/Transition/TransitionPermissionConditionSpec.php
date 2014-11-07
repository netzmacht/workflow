<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Security\Role;
use Netzmacht\Workflow\Security\User;
use Netzmacht\Workflow\Flow\Condition\Transition\TransitionPermissionCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TransitionPermissionConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin TransitionPermissionCondition
 */
class TransitionPermissionConditionSpec extends ObjectBehavior
{
    function let(User $user, Transition $transition, Role $role)
    {
        $this->beConstructedWith($user);

        $transition->getRoles()->willReturn(array($role));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\TransitionPermissionCondition');
    }

    function it_is_a_transition_condition()
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Transition\Condition');
    }

    function it_matches_if_transition_any_of_the_transition_roles_matches(
        Transition $transition,
        User $user,
        Role $role,
        Role $notGranted,
        Item $item,
        Context $context
    )
    {
        $user->hasRole($role)->willReturn(true);
        $user->hasRole($notGranted)->willReturn(false);

        $transition->getRoles()->willReturn(array($notGranted, $role));

        $this->match($transition, $item, $context)->shouldReturn(true);
        $this->shouldNotHaveError();
    }

    function it_does_not_match_if_transition_has_no_role(
        Transition $transition,
        Item $item,
        Context $context
    )
    {
        $transition->getRoles()->willReturn(array());

        $this->match($transition, $item, $context)->shouldReturn(false);
        $this->shouldHaveError();
    }
}
