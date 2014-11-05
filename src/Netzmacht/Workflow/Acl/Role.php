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
     * Name of the corresponding workflow.
     *
     * @var string
     */
    private $workflowName;

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
     * Set the workflow name.
     *
     * This method is not meant to be called from the user. It is called when a role is added to the workflow.
     *
     * @param string $workflowName Name of the workflow.
     *
     * @return $this
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = $workflowName;

        return $this;
    }

    /**
     * Get full name will combine workflow name and role name.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->workflowName . '::' . $this->getName();
    }
}
