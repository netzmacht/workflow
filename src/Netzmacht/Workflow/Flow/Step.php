<?php

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Acl\Role;
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
     * Assigned role.
     *
     * @var Role
     */
    private $role;

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
        $this->final = (bool)$final;

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
     * Assign step to a role.
     *
     * @param Role $role
     *
     * @return $this
     */
    public function assignTo(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Consider if step is assigned to a specific role.
     *
     * @param Role $role The role to check.
     *
     * @return bool
     */
    public function isAssignedTo(Role $role)
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->equals($role);
    }

    /**
     * Get the assigned role. Returns null if no role is assigned.
     *
     * @return Role|null
     */
    public function getRole()
    {
        return $this->role;
    }
}
