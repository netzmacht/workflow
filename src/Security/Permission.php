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

declare(strict_types=1);

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
    protected function __construct(string $workflowName, string $permissionId)
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
    public static function forWorkflow(Workflow $workflow, string $permissionId): self
    {
        return static::forWorkflowName($workflow->getName(), $permissionId);
    }

    /**
     * Reconstruct permission from a string representation.
     *
     * @param string $workflowName Workflow name.
     * @param string $permissionId Permission id.
     *
     * @return static
     */
    public static function forWorkflowName(string $workflowName, string $permissionId): self
    {
        Assertion::notBlank($workflowName);
        Assertion::notBlank($permissionId);

        self::guardValidPermission($workflowName, $permissionId);

        return new static($workflowName, $permissionId);
    }

    /**
     * Reconstruct permission from a string representation.
     *
     * @param string $permission Permission string representation.
     *
     * @return static
     */
    public static function fromString(string $permission): self
    {
        list($workflowName, $permissionId) = explode(':', $permission);

        $message = sprintf(
            'Invalid permission string given. Expected "workflowName:permissionId, got "%s"".',
            $permission
        );

        self::guardValidPermission($workflowName, $permissionId, $message);

        return new static($workflowName, $permissionId);
    }

    /**
     * Get the permission id.
     *
     * @return string
     */
    public function getPermissionId(): string
    {
        return $this->permissionId;
    }

    /**
     * Get the workflow name.
     *
     * @return string
     */
    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    /**
     * Cast permission to a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->workflowName . ':' . $this->permissionId;
    }

    /**
     * Consider if permission equals with another one.
     *
     * @param Permission $permission Permission to check against.
     *
     * @return bool
     */
    public function equals(Permission $permission): bool
    {
        return ((string) $this === (string) $permission);
    }

    /**
     * Guard that permissoin values are valid.
     *
     * @param string      $workflowName The workflow name.
     * @param string      $permissionId The permission id.
     * @param string|null $message      Optional error message.
     *
     * @return void
     */
    protected static function guardValidPermission(string $workflowName, string $permissionId, string $message = null)
    {
        Assertion::notBlank($workflowName, $message);
        Assertion::notBlank($permissionId, $message);
    }
}
