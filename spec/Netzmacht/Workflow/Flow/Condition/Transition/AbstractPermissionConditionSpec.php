<?php

namespace spec\Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Acl\User;
use Netzmacht\Workflow\Flow\Condition\Transition\AbstractPermissionCondition;
use Netzmacht\Workflow\Flow\Context;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Transition;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class AbstractPermissionConditionSpec
 * @package spec\Netzmacht\Workflow\Flow\Condition\Transition
 * @mixin PermissionCondition
 */
class AbstractPermissionConditionSpec extends ObjectBehavior
{
    function let(User $user, Role $role)
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

    function it_gets_the_user(User $user)
    {
        $this->getUser()->shouldReturn($user);
    }

    function it_grants_permission_for_a_single_role(User $user, Role $role)
    {
        $user->isGranted($role)->willReturn(true);
        $this->isGranted($role)->shouldReturn(true);
    }

    function it_grants_permission_for_one_role_is_granted(User $user, Role $role, Role $anotherRole)
    {
        $user->isGranted($anotherRole)->willReturn(false);
        $user->isGranted($role)->willReturn(true);

        $this->isGranted(array($anotherRole, $role))->shouldReturn(true);
    }

    function it_does_not_grant_for_empty_roles()
    {
        $this->isGranted(array())->shouldReturn(false);
    }
}

class PermissionCondition extends AbstractPermissionCondition
{
    /**
     * Consider if condition matches for the given entity.
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return bool
     */
    public function match(Transition $transition, Item $item, Context $context)
    {
        // TODO: Implement match() method.
    }

    /**
     * Describes an failed condition.
     *
     * It returns an array with 2 parameters. First one is the error message code. The second one are the params to
     * be replaced in the message.
     *
     * Example return array('transition.condition.example', array('name', 'value'));
     *
     * @param Transition $transition The transition being in.
     * @param Item       $item       The entity being transits.
     * @param Context    $context    The transition context.
     *
     * @return array
     */
    public function describeError(Transition $transition, Item $item, Context $context)
    {
        // TODO: Implement describeError() method.
    }
}
