<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

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
     * @param string      $name         Name of the role.
     * @param string      $workflowName Name of the workflow the role belongs to.
     * @param string|null $label        Optional role label.
     * @param array       $config       Optional role configuration.
     */
    public function __construct($name, $workflowName, $label = null, array $config = array())
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
    public function addPermission(Permission $permission)
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
    public function hasPermission(Permission $permission)
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
     * @param Permission[] $permissions Set of permissions.
     *
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        Assertion::allIsInstanceOf($permissions, 'Netzmacht\Workflow\Security\Permission');

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
     * @return Permission[]
     */
    public function getPermissions()
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
    public function removePermission(Permission $permission)
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
    public function equals(Role $role)
    {
        return $this->getFullName() == $role->getFullName();
    }

    /**
     * Get the workflow name.
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Get full name will combine workflow name and role name.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->workflowName . ':' . $this->getName();
    }
}
