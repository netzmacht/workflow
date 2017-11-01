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
use Netzmacht\Workflow\Base;

/**
 * Class Role describes an user role.
 *
 * @package Netzmacht\Workflow\Acl
 */
class Role extends Base
{
    /**
     * Name of the corresponding workflow.
     *
     * @var string
     */
    private $workflowName;

    /**
     * Array of permissions.
     *
     * @var Permission[]
     */
    private $permissions = array();

    /**
     * Construct.
     *
     * @param string $name         Name of the role.
     * @param string $workflowName Name of the workflow the role belongs to.
     * @param string $label        Optional role label.
     * @param array  $config       Optional role configuration.
     */
    public function __construct(string $name, string $workflowName, string $label = '', array $config = array())
    {
        parent::__construct($name, $label, $config);

        $this->workflowName = $workflowName;
    }

    /**
     * Add a permission to the role.
     *
     * @param Permission $permission Permission.
     *
     * @return $this
     */
    public function addPermission(Permission $permission): self
    {
        if (!$this->hasPermission($permission)) {
            $this->permissions[] = $permission;
        }

        return $this;
    }

    /**
     * Consider if role has a permission.
     *
     * @param Permission $permission The permission.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission): bool
    {
        foreach ($this->permissions as $assigned) {
            if ($assigned->equals($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Consider if role has a set of permissions.
     *
     * @param iterable|Permission[] $permissions Set of permissions.
     *
     * @return bool
     */
    public function hasPermissions(iterable $permissions): bool
    {
        Assertion::allIsInstanceOf($permissions, Permission::class);

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions.
     *
     * @return Permission[]|iterable
     */
    public function getPermissions(): iterable
    {
        return $this->permissions;
    }

    /**
     * Remove a permission.
     *
     * @param Permission $permission The permission.
     *
     * @return $this
     */
    public function removePermission(Permission $permission): self
    {
        foreach ($this->permissions as $key => $assigned) {
            if ($assigned->equals($permission)) {
                unset($this->permissions[$key]);

                break;
            }
        }

        return $this;
    }

    /**
     * Consider if role equals to another role.
     *
     * @param Role $role Role to compare with.
     *
     * @return bool
     */
    public function equals(Role $role): bool
    {
        return $this->getFullName() == $role->getFullName();
    }

    /**
     * Get the workflow name.
     *
     * @return string
     */
    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    /**
     * Get full name will combine workflow name and role name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->workflowName . ':' . $this->getName();
    }
}
