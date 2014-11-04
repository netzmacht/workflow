<?php

namespace spec\Netzmacht\Workflow\Acl;

use Netzmacht\Workflow\Acl\Role;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RoleSpec
 * @package spec\Netzmacht\Workflow\Acl
 * @mixin Role
 */
class RoleSpec extends ObjectBehavior
{
    const NAME = 'role';

    function let()
    {
        $this->beConstructedWith(static::NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Acl\Role');
    }

    function it_behaves_like_base()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Base');
    }

    function it_equals_to_identical_role(Role $role)
    {
        $role->getName()->willReturn(static::NAME);

        $this->equals($role);
    }

    function it_does_not_equal_to_different_role(Role $role)
    {
        $role->getName()->willReturn('other_role');

        $this->equals($role);
    }
}
