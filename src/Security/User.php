<?php

/**
 * Workflow library.
 *
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014-2017 netzmacht David Molineus
 * @license    LGPL 3.0 https://github.com/netzmacht/workflow
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Workflow\Security;

use Assert\Assertion;

/**
 * Class User is a basic implementation of the user interface to provide an in memory user.
 *
 * @package Netzmacht\Workflow\Security
 */
class User
{
    /**
     * Permission roles which were granted.
     *
     * @var Role[]
     */
    private $roles = array();

    /**
     * Consider if user has a role.
     *
     * @param Role $role Check this role.
     *
     * @return bool
     */
    public function hasRole(Role $role): bool
    {
        foreach ($this->roles as $granted) {
            if ($granted->equals($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Consider if user has a permission.
     *
     * @param Permission $permission Permission which is required.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all required permissions.
     *
     * @param iterable|Permission[] $permissions Permissions which are required.
     *
     * @return bool
     */
    public function hasPermissions(iterable $permissions): bool
    {
        Assertion::allIsInstanceOf($permissions, Permission::class);

        $granted = array();

        foreach ($permissions as $index => $permission) {
            foreach ($this->roles as $role) {
                if ($role->hasPermission($permission)) {
                    $granted[$index] = true;

                    continue;
                }
            }
        }

        return (count($granted) === count($permissions));
    }

    /**
     * Grant access to a role.
     *
     * @param Role $role Role to be granted.
     *
     * @return $this
     */
    public function assign(Role $role): self
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Get user roles.
     *
     * @return Role[]|iterable
     */
    public function getRoles(): iterable
    {
        return $this->roles;
    }

    /**
     * Withdraw access.
     *
     * @param Role $role Role to be withdrawn.
     *
     * @return $this
     */
    public function reject(Role $role): self
    {
        foreach ($this->roles as $index => $granted) {
            if ($granted->equals($role)) {
                unset($this->roles[$index]);

                break;
            }
        }

        return $this;
    }
}
