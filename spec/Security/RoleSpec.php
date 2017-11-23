<?php

namespace spec\Netzmacht\Workflow\Security;

use Netzmacht\Workflow\Flow\Security\Permission;
use Netzmacht\Workflow\Security\Role;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RoleSpec
 * @package spec\Netzmacht\Workflow\Acl
 */
class RoleSpec extends ObjectBehavior
{
    const NAME = 'role';
    const WORKFLOW = 'workflow';

    function let()
    {
        $this->beConstructedWith(static::NAME, static::WORKFLOW);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Security\Role');
    }

    function it_behaves_like_base()
    {
        $this->shouldHaveType('Netzmacht\Workflow\Flow\Base');
    }

    function it_belongs_to_an_workflow()
    {
        $this->getWorkflowName()->shouldReturn(static::WORKFLOW);
        $this->getFullName()->shouldReturn(static::WORKFLOW . ':' . static::NAME);
    }

    function it_equals_to_identical_role(Role $role)
    {
        $role->getFullName()->willReturn(static::NAME);

        $this->equals($role);
    }

    function it_does_not_equal_to_different_role(Role $role)
    {
        $role->getFullName()->willReturn('other_role');

        $this->equals($role);
    }

    function it_contains_permission(Permission $permission)
    {
        $permission->equals($permission)->willReturn(true);

        $this->hasPermission($permission)->shouldReturn(false);
        $this->hasPermissions(array($permission))->shouldReturn(false);

        $this->addPermission($permission)->shouldReturn($this);

        $this->hasPermission($permission)->shouldReturn(true);
        $this->hasPermissions(array($permission))->shouldReturn(true);

        $this->getPermissions()->shouldReturn(array($permission));
    }

    function it_removes_condition(Permission $permission)
    {
        $permission->equals($permission)->willReturn(true);

        $this->addPermission($permission);
        $this->hasPermission($permission)->shouldReturn(true);

        $this->removePermission($permission)->shouldReturn($this);
        $this->hasPermission($permission)->shouldReturn(false);
    }
}
