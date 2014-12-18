<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Security\Permission;
use Netzmacht\Workflow\Base;

/**
 * Class Step defines fixed step in the workflow process.
 *
 * @package Netzmacht\Workflow\Flow
 */
class Step extends Base
{
    /**
     * The allowed transition names.
     *
     * @var array
     */
    private $allowedTransitions = array();

    /**
     * Step is a final step.
     *
     * @var bool
     */
    private $final = false;

    /**
     * Assigned permission.
     *
     * @var Permission|null
     */
    private $permission;

    /**
     * Consider if step is final.
     *
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Mark step as final.
     *
     * @param boolean $final Step is a final step.
     *
     * @return $this
     */
    public function setFinal($final)
    {
        $this->final = (bool) $final;

        return $this;
    }

    /**
     * Allow a transition.
     *
     * @param string $transitionName The name of the allowed transition.
     *
     * @return $this
     */
    public function allowTransition($transitionName)
    {
        if (!in_array($transitionName, $this->allowedTransitions)) {
            $this->allowedTransitions[] = $transitionName;
        }

        return $this;
    }

    /**
     * Disallow a transition.
     *
     * @param string $transitionName The name of the disallowed transition.
     *
     * @return $this
     */
    public function disallowTransition($transitionName)
    {
        $key = array_search($transitionName, $this->allowedTransitions);

        if ($key !== false) {
            unset($this->allowedTransitions[$key]);
            $this->allowedTransitions = array_values($this->allowedTransitions);
        }

        return $this;
    }

    /**
     * Get all allowed transition names.
     *
     * @return array
     */
    public function getAllowedTransitions()
    {
        if ($this->isFinal()) {
            return array();
        }

        return $this->allowedTransitions;
    }

    /**
     * Consider if transition is allowed.
     *
     * @param string $transitionName The name of the checked transition.
     *
     * @return bool
     */
    public function isTransitionAllowed($transitionName)
    {
        if ($this->isFinal()) {
            return false;
        }

        return in_array($transitionName, $this->allowedTransitions);
    }

    /**
     * Consider if step has a specific permission.
     *
     * @param Permission $permission Permission to be checked.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission)
    {
        if ($this->permission) {
            return $this->permission->equals($permission);
        }

        return false;
    }

    /**
     * Get permission of the step. If none is assigned it returns null.
     *
     * @return Permission|null
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set permission.
     *
     * @param Permission $permission Permission to be set.
     *
     * @return $this
     */
    public function setPermission(Permission $permission)
    {
        $this->permission = $permission;

        return $this;
    }
}
