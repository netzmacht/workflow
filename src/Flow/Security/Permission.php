<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow\Security;

use Assert\Assertion;
use Netzmacht\Workflow\Flow\Workflow;

use function explode;
use function sprintf;

/**
 * Class Permission describes a permission in a workflow.
 */
final class Permission
{
    /**
     * The workflow name.
     */
    private string $workflowName;

    /**
     * The permission id.
     */
    private string $permissionId;

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
        [$workflowName, $permissionId] = explode(':', $permission);

        $message = sprintf(
            'Invalid permission string given. Expected "workflowName:permissionId, got "%s"".',
            $permission,
        );

        self::guardValidPermission($workflowName, $permissionId, $message);

        return new static($workflowName, $permissionId);
    }

    /**
     * Get the permission id.
     */
    public function getPermissionId(): string
    {
        return $this->permissionId;
    }

    /**
     * Get the workflow name.
     */
    public function getWorkflowName(): string
    {
        return $this->workflowName;
    }

    /**
     * Cast permission to a string representation.
     */
    public function __toString(): string
    {
        return $this->workflowName . ':' . $this->permissionId;
    }

    /**
     * Consider if permission equals with another one.
     *
     * @param Permission $permission Permission to check against.
     */
    public function equals(Permission $permission): bool
    {
        return (string) $this === (string) $permission;
    }

    /**
     * Guard that permission values are valid.
     *
     * @param string      $workflowName The workflow name.
     * @param string      $permissionId The permission id.
     * @param string|null $message      Optional error message.
     */
    protected static function guardValidPermission(
        string $workflowName,
        string $permissionId,
        string|null $message = null,
    ): void {
        Assertion::notBlank($workflowName, $message);
        Assertion::notBlank($permissionId, $message);
    }
}
