<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Security\Permission;
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
    const ERROR_COLLECTION_CLASS = 'Netzmacht\Workflow\Data\ErrorCollection';

    function let(User $user, Transition $transition, Permission $permission)
    {
        $this->beConstructedWith($user);

        $transition->getPermission()->willReturn($permission);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\TransitionPermissionCondition');
    }

    function it_is_a_transition_condition()
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Transition\Condition');
    }

    function it_matches_if_user_has_transition_permissions(
        Transition $transition,
        User $user,
        Permission $permission,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $user->hasPermission($permission)->willReturn(true);

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(true);
    }

    function it_does_not_match_if_user_has_not_transition_permissions(
        Transition $transition,
        User $user,
        Permission $permission,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    ) {
        $user->hasPermission($permission)->willReturn(false);

        $permission->__toString()->willReturn('workflow/permission');

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }

    function it_does_not_match_if_transition_has_no_permission(
        Transition $transition,
        Item $item,
        Context $context,
        ErrorCollection $errorCollection
    )
    {
        $transition->getPermission()->willReturn(null);

        $errorCollection->addError(Argument::cetera())->shouldBeCalled();

        $this->match($transition, $item, $context, $errorCollection)->shouldReturn(false);
    }
}
