<?php

namespace spec\Netzmacht\Workflow\Security;

use Netzmacht\Workflow\Security\User;
use Netzmacht\Workflow\Security\Role;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class InMemoryUserSpec
 * @package spec\Netzmacht\Workflow\Acl
 * @mixin User
 */
class UserSpec extends ObjectBehavior
{
    function let(Role $role)
    {
        $role->equals($role)->willReturn(true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Security\User');
    }

    function it_grants_access_to_a_role(Role $role)
    {
        $this->assign($role)->shouldReturn($this);
        $this->hasRole($role)->shouldReturn(true);
    }

    function it_knows_if_role_is_granted(Role $role)
    {
        $this->hasRole($role)->shouldReturn(false);
    }

    function it_withdraws_access(Role $role)
    {
        $this->assign($role);
        $this->hasRole($role)->shouldReturn(true);
        $this->reject($role);
        $this->hasRole($role)->shouldReturn(false);
    }
}
