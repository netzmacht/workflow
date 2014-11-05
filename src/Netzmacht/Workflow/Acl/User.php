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
 * Interface User describes basic user in an workflow environment.
 *
 * @package Netzmacht\Workflow\Acl
 */
interface User
{
    /**
     * Consider if user has a role.
     *
     * @param Role $role Check this role.
     *
     * @return bool
     */
    public function isGranted(Role $role);

    /**
     * Grant access to a role.
     *
     * @param Role $role Role to be granted.
     *
     * @return $this
     */
    public function grantAccess(Role $role);

    /**
     * Withdraw access.
     *
     * @param Role $role Role to be withdrawn.
     *
     * @return $this
     */
    public function withdrawAccess(Role $role);
}
