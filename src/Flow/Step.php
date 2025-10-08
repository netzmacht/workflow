<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Security\Permission;

use function array_search;
use function array_values;
use function in_array;

/**
 * Class Step defines a fixed step in the workflow process.
 */
final class Step extends Base
{
    /**
     * The allowed transition names.
     *
     * @var list<string>
     */
    private array $allowedTransitions = [];

    /**
     * Step is a final step.
     */
    private bool $final = false;

    /**
     * Assigned permission.
     */
    private Permission|null $permission = null;

    /**
     * The workflow name.
     *
     * For BC reasons it might be null.
     */
    private string|null $workflowName;

    /**
     * Construct.
     *
     * @param string               $name         Name of the element.
     * @param string               $label        Label of the element.
     * @param array<string, mixed> $config       Configuration values.
     * @param string|null          $workflowName Name of the corresponding workflow. For BC reasons it might be null.
     */
    public function __construct(string $name, string $label = '', array $config = [], string|null $workflowName = null)
    {
        parent::__construct($name, $label, $config);

        $this->workflowName = $workflowName;
    }

    /**
     * Consider if step is final.
     */
    public function isFinal(): bool
    {
        return $this->final;
    }

    /**
     * Mark step as final.
     *
     * @param bool $final Step is a final step.
     *
     * @return $this
     */
    public function setFinal(bool $final): self
    {
        $this->final = $final;

        return $this;
    }

    /**
     * Allow a transition.
     *
     * @param string $transitionName The name of the allowed transition.
     *
     * @return $this
     */
    public function allowTransition(string $transitionName): self
    {
        if (! in_array($transitionName, $this->allowedTransitions)) {
            $this->allowedTransitions[] = $transitionName;
        }

        return $this;
    }

    /**
     * Get workflow name.
     */
    public function getWorkflowName(): string|null
    {
        return $this->workflowName;
    }

    /**
     * Disallow a transition.
     *
     * @param string $transitionName The name of the disallowed transition.
     *
     * @return $this
     */
    public function disallowTransition(string $transitionName): self
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
     * @return list<string>
     */
    public function getAllowedTransitions(): array
    {
        if ($this->isFinal()) {
            return [];
        }

        return $this->allowedTransitions;
    }

    /**
     * Consider if transition is allowed.
     *
     * @param string $transitionName The name of the checked transition.
     */
    public function isTransitionAllowed(string $transitionName): bool
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
     */
    public function hasPermission(Permission $permission): bool
    {
        if ($this->permission) {
            return $this->permission->equals($permission);
        }

        return false;
    }

    /**
     * Get permission of the step. If none is assigned it returns null.
     */
    public function getPermission(): Permission|null
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
    public function setPermission(Permission $permission): self
    {
        $this->permission = $permission;

        return $this;
    }
}
