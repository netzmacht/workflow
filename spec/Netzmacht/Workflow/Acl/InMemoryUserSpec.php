<?php

namespace spec\Netzmacht\Workflow\Acl;

use Netzmacht\Workflow\Acl\InMemoryUser;
use Netzmacht\Workflow\Acl\Role;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class InMemoryUserSpec
 * @package spec\Netzmacht\Workflow\Acl
 * @mixin InMemoryUser
 */
class InMemoryUserSpec extends ObjectBehavior
{
    function let(Role $role)
    {
        $role->equals($role)->willReturn(true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Acl\InMemoryUser');
    }

    function it_grants_access_to_a_role(Role $role)
    {
        $this->grantAccess($role)->shouldReturn($this);
        $this->isGranted($role)->shouldReturn(true);
    }

    function it_knows_if_role_is_granted(Role $role)
    {
        $this->isGranted($role)->shouldReturn(false);
    }

    function it_withdraws_access(Role $role)
    {
        $this->isGranted($role)->shouldReturn(false);
        $this->grantAccess($role);
        $this->isGranted($role)->shouldReturn(true);
    }
}
