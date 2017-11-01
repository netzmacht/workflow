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

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Security\User;

/**
 * Class AbstractPermissionCondition is the base class for permission related conditions.
 *
 * It provides the user instance as object property.
 *
 * @package Netzmacht\Workflow\Flow\Condition\Transition
 */
abstract class AbstractPermissionCondition implements Condition
{
    /**
     * Security user instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Default value.
     *
     * Default value is used if no permission is given.
     *
     * @var bool
     */
    protected $default = false;

    /**
     * Construct.
     *
     * @param User $user Security user instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Set default value.
     *
     * @param bool $access Default access value if no permission is found.
     *
     * @return $this
     */
    public function grantAccessByDefault($access)
    {
        $this->default = (bool) $access;

        return $this;
    }

    /**
     * Get default value.
     *
     * @return bool
     */
    public function isGrantedByDefault()
    {
        return $this->default;
    }

    /**
     * Check a specific transition. Check if access is granted by default if no permission is given.
     *
     * @param Permission|null $permission Permission to check.
     *
     * @return bool
     */
    protected function checkPermission(Permission $permission = null)
    {
        if ($permission) {
            if ($this->user->hasPermission($permission)) {
                return true;
            }
        } elseif ($this->isGrantedByDefault()) {
            return true;
        }

        return false;
    }
}
