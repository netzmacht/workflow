<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Data\ErrorCollection;
use Netzmacht\Workflow\Security\User;
use Netzmacht\Workflow\Flow\Condition\Transition\AbstractPermissionCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;

/**
 * Class AbstractPermissionConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 */
class AbstractPermissionConditionSpec extends ObjectBehavior
{
    function let(User $user)
    {
        $this->beAnInstanceOf('spec\Netzmacht\Workflow\Flow\Condition\Transition\PermissionCondition');
        $this->beConstructedWith($user);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Condition\Transition\AbstractPermissionCondition');
    }

    function it_is_a_transition_condition()
    {
        $this->shouldImplement('Netzmacht\Workflow\Flow\Condition\Transition\Condition');
    }

    function it_does_not_grant_by_default_for_an_empty_permission()
    {
        $this->checkPermission()->shouldReturn(false);
    }

    function it_has_option_to_grant_permission_by_default(User $user)
    {
        $this->beConstructedWith($user, true);
        $this->checkPermission()->shouldReturn(true);
    }
}

class PermissionCondition extends AbstractPermissionCondition
{
    public function match(Transition $transition, Item $item, Context $context, ErrorCollection $errorCollection): bool
    {
        return false;
    }
}
