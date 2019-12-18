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

namespace Netzmacht\Workflow\Flow;

use Netzmacht\Workflow\Flow\Condition\Transition\Condition;
use Netzmacht\Workflow\Flow\Security\Permission;

/**
 * Class Transition handles the transition from a step to another.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
interface Transition
{
    /**
     * Get element label.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Set the label.
     *
     * @param string $label The label.
     *
     * @return $this
     */
    public function setLabel(string $label): self;

    /**
     * Get element name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set a config value.
     *
     * @param string $name  Config property name.
     * @param mixed  $value Config property value.
     *
     * @return $this
     */
    public function setConfigValue(string $name, $value): self;

    /**
     * Get a config value.
     *
     * @param string $name    Config property name.
     * @param mixed  $default Default value which is returned if config is not set.
     *
     * @return mixed
     */
    public function getConfigValue(string $name, $default = null);

    /**
     * Consider if config value isset.
     *
     * @param string $name Name of the config value.
     *
     * @return bool
     */
    public function hasConfigValue(string $name): bool;

    /**
     * Add multiple config properties.
     *
     * @param array $values Config values.
     *
     * @return $this
     */
    public function addConfig(array $values): self;

    /**
     * Remove a config property.
     *
     * @param string $name Config property name.
     *
     * @return $this
     */
    public function removeConfigValue(string $name): self;

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Get the workflow.
     *
     * @return Workflow
     */
    public function getWorkflow(): Workflow;

    /**
     * Get the target step.
     *
     * @return Step
     */
    public function getStepTo():? Step;

    /**
     * Get the condition.
     *
     * @return Condition|null
     */
    public function getCondition():? Condition;

    /**
     * Add a condition.
     *
     * @param Condition $condition The new condition.
     *
     * @return $this
     */
    public function addCondition(Condition $condition): self;

    /**
     * Get the precondition.
     *
     * @return Condition
     */
    public function getPreCondition():? Condition;

    /**
     * Add a precondition precondition.
     *
     * @param Condition $preCondition The new precondition.
     *
     * @return $this
     */
    public function addPreCondition(Condition $preCondition): self;

    /**
     * Consider if user input is required.
     *
     * @param Item $item Workflow item.
     *
     * @return array
     */
    public function getRequiredPayloadProperties(Item $item): array;

    /**
     * Validate the given item and context (payload properties).
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     *
     * @return bool
     */
    public function validate(Item $item, Context $context): bool;

    /**
     * Execute the transition for the given item and context (payload properties).
     *
     * @param Item    $item    Workflow item.
     * @param Context $context Transition context.
     *
     * @return State
     */
    public function execute(Item $item, Context $context): State;

    /**
     * Consider if transition is allowed.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function isAllowed(Item $item, Context $context): bool;

    /**
     * Consider if transition is available.
     *
     * If a transition can be available but it is not allowed depending on the user input.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function isAvailable(Item $item, Context $context): bool;

    /**
     * Check the precondition.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function checkPreCondition(Item $item, Context $context): bool;

    /**
     * Check the condition.
     *
     * @param Item    $item    The Item.
     * @param Context $context The transition context.
     *
     * @return bool
     */
    public function checkCondition(Item $item, Context $context): bool;

    /**
     * Set a permission to the transition.
     *
     * @param Permission $permission Permission being assigned.
     *
     * @return $this
     */
    public function setPermission(Permission $permission): self;

    /**
     * Consider if permission is assigned to transition.
     *
     * @param Permission $permission Permission being check.
     *
     * @return bool
     */
    public function hasPermission(Permission $permission): bool;

    /**
     * Get assigned permission. Returns null if no transition is set.
     *
     * @return Permission|null
     */
    public function getPermission():? Permission;
}
