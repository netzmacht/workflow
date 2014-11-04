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

use Netzmacht\Workflow\Base;

/**
 * Class Role describes an user role.
 *
 * @package Netzmacht\Workflow\Acl
 */
class Role extends Base
{
    /**
     * Consider if role equals to another role.
     *
     * @param Role $role Role to compare with.
     *
     * @return bool
     */
    public function equals(Role $role)
    {
        return $this->getName() == $role->getName();
    }
}
