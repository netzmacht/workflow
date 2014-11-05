<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow\Condition\Transition;

use Assert\Assertion;
use Netzmacht\Workflow\Acl\Role;
use Netzmacht\Workflow\Acl\User;

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
    private $user;

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
     * Get security user instance.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Consider if role is granted.
     *
     * @param Role[]|Role $roles Permission roles.
     *
     * @return bool
     */
    public function isGranted($roles)
    {
        $roles = $this->unifyRolesArgument($roles);
        Assertion::allIsInstanceOf($roles, 'Netzmacht\Workflow\Acl\Role');

        foreach ($roles as $role) {
            if ($this->user->isGranted($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Always get roles as array.
     *
     * @param Role[]|Role $roles Permission roles.
     *
     * @return Role[]
     */
    protected function unifyRolesArgument($roles)
    {
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        return $roles;
    }
}
