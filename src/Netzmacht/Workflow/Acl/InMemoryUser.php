<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Acl;

/**
 * Class InMemoryUser is a basic implementation of the user interface to provide an in memory user.
 *
 * @package Netzmacht\Workflow\Acl
 */
class InMemoryUser implements User
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
    public function isGranted(Role $role)
    {
        foreach ($this->roles as $granted) {
            if ($granted->equals($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grant access to a role.
     *
     * @param Role $role Role to be granted.
     *
     * @return $this
     */
    public function grantAccess(Role $role)
    {
        if (!$this->isGranted($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Withdraw access.
     *
     * @param Role $role Role to be withdrawn.
     *
     * @return $this
     */
    public function withdrawAccess(Role $role)
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
