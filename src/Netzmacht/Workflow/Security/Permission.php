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
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Class Permission describes a permission in a workflow.
 *
 * @package Netzmacht\Workflow\Security
 */
class Permission
{
    /**
     * The workflow name.
     *
     * @var string
     */
    private $workflowName;

    /**
     * The permission id.
     *
     * @var string
     */
    private $permissionId;

    /**
     * Construct.
     *
     * @param string $workflowName The workflow name.
     * @param string $permissionId The permission id.
     */
    protected function __construct($workflowName, $permissionId)
    {
        $this->workflowName = $workflowName;
        $this->permissionId = $permissionId;
    }

    /**
     * Create a permission for a workflow.
     *
     * @param Workflow $workflow     Workflow to which the permission belongs to.
     * @param string   $permissionId The permission id.
     *
     * @return static
     */
    public static function forWorkflow(Workflow $workflow, $permissionId)
    {
        return new static($workflow->getName(), $permissionId);
    }

    /**
     * Reconstruct permission from a string representation.
     *
     * @param string $permission Permission string representation.
     *
     * @return static
     */
    public static function fromString($permission)
    {
        list($workflowName, $permissionId) = explode('/', $permission);

        Assertion::notBlank($workflowName);
        Assertion::notBlank($permissionId);

        return new static($workflowName, $permissionId);
    }

    /**
     * Get the permission id.
     *
     * @return string
     */
    public function getPermissionId()
    {
        return $this->permissionId;
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
     * Cast permission to a string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->workflowName . '/' . $this->permissionId;
    }

    /**
     * Consider if permission equals with another one.
     *
     * @param Permission $permission Permission to check against.
     *
     * @return bool
     */
    public function equals(Permission $permission)
    {
        return ((string) $this === (string) $permission);
    }
}
