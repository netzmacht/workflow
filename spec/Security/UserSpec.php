<?php

namespace spec\Netzmacht\Workflow\Security;

use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\Role;
use PhpSpec\ObjectBehavior;

/**
 * Class UserSpec
 *
 * @package spec\Netzmacht\Workflow\Acl
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

    function it_assigns_a_role(Role $role)
    {
        $this->assign($role)->shouldReturn($this);
        $this->hasRole($role)->shouldReturn(true);
    }

    function it_knows_if_role_is_assigned(Role $role)
    {
        $this->hasRole($role)->shouldReturn(false);
    }

    function it_rejects_role(Role $role)
    {
        $this->assign($role);
        $this->hasRole($role)->shouldReturn(true);
        $this->reject($role);
        $this->hasRole($role)->shouldReturn(false);
    }

    function it_contains_roles(Role $role)
    {
        $this->assign($role);
        $this->getRoles()->shouldReturn([$role]);
    }

    function it_checks_permission(Role $role, Permission $permission)
    {
        $this->assign($role);
        $role->hasPermission($permission)->willReturn(true);

        $this->hasPermission($permission)->shouldReturn(true);
    }

    function it_checks_permission_until_found(Role $role, Permission $permission, Role $roleB)
    {
        $role->equals($roleB)->willReturn(false);

        $this->assign($role);
        $this->assign($roleB);

        $roleB->hasPermission($permission)->willReturn(false);
        $role->hasPermission($permission)->willReturn(true);

        $this->hasPermission($permission)->shouldReturn(true);
    }

    function it_has_no_permission_without_roles(Permission $permission)
    {
        $this->hasPermission($permission)->shouldReturn(false);
    }

    function it_has_no_permission_if_role_has_not(Role $role, Permission $permission)
    {
        $role->hasPermission($permission)->willReturn(false);

        $this->assign($role);
        $this->hasPermission($permission)->shouldReturn(false);
    }

    function it_checks_multiple_permissions_with_multiple_roles(
        Role $role,
        Permission $permission,
        Permission $permissionA,
        Role $roleB
    ) {
        $role->hasPermission($permission)->willReturn(false);
        $role->hasPermission($permissionA)->willReturn(true);

        $roleB->hasPermission($permission)->willReturn(true);
        $roleB->hasPermission($permissionA)->willReturn(false);

        $role->equals($roleB)->willReturn(false);

        $this->assign($role);
        $this->assign($roleB);

        $this->hasPermissions([$permission, $permissionA])->shouldReturn(true);
    }

    function it_requires_all_multiple_permissions(
        Role $role,
        Permission $permission,
        Permission $permissionA,
        Role $roleB
    ) {
        $role->hasPermission($permission)->willReturn(false);
        $role->hasPermission($permissionA)->willReturn(true);

        $roleB->hasPermission($permission)->willReturn(false);
        $roleB->hasPermission($permissionA)->willReturn(true);

        $role->equals($roleB)->willReturn(false);

        $this->assign($role);
        $this->assign($roleB);

        $this->hasPermissions([$permission, $permissionA])->shouldReturn(false);
    }
}
