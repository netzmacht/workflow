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
}
