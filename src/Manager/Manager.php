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

namespace Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Exception\WorkflowNotFound;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;

/**
 * Class Manager handles a set of workflows.
 *
 * Usually there will a different workflow manager for different workflow types. The manager is the API entry point
 * when using the workflow API.
 *
 * @package Netzmacht\Workflow
 */
interface Manager
{
    /**
     * Create a TransitionHandler for the given item.
     *
     * If no matching workflow definition is found, null will be returned.
     *
     * @param Item   $item           The current workflow item.
     * @param string $transitionName Transition name, required if workflow has already started.
     * @param bool   $changeWorkflow If true the item is detached from current workflow if another workflow is used.
     *
     * @throws WorkflowException If something went wrong.
     *
     * @return TransitionHandler
     */
    public function handle(Item $item, string $transitionName = null, bool $changeWorkflow = false): ?TransitionHandler;

    /**
     * Create a TransitionHandler for the given item and workflow.
     *
     * If no matching workflow definition is found, null will be returned.
     *
     * @param Workflow $workflow     The desired workflow.
     * @param Item $item             The current workflow item.
     * @param string $transitionName Transition name, required if workflow has already started.
     * @param bool $changeWorkflow   If true the item is detached from current workflow if another workflow is used.
     * @return TransitionHandler
     */
    public function createTransitionHandler(Workflow $workflow, Item $item, string $transitionName, bool $changeWorkflow): TransitionHandler;

    /**
     * Add a workflow to the manager.
     *
     * @param Workflow $workflow The workflow being added.
     *
     * @return $this
     */
    public function addWorkflow(Workflow $workflow): self;

    /**
     * Get a workflow for the given entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     *
     * @return Workflow
     *
     * @throws WorkflowNotFound When no workflow is found.
     */
    public function getWorkflow(EntityId $entityId, $entity): Workflow;

    /**
     * Get Workflow by its name.
     *
     * @param string $name Name of workflow.
     *
     * @return Workflow
     *
     * @throws WorkflowNotFound When no workflow is found.
     */
    public function getWorkflowByName(string $name): Workflow;

    /**
     * Get workflow by item.
     *
     * @param Item $item Workflow item.
     *
     * @return Workflow
     *
     * @throws WorkflowNotFound When no workflow is found.
     */
    public function getWorkflowByItem(Item $item): Workflow;

    /**
     * Consider if entity has an workflow.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     *
     * @return bool
     */
    public function hasWorkflow(EntityId $entityId, $entity): bool;

    /**
     * Get all registered workflows.
     *
     * @return Workflow[]|iterable
     */
    public function getWorkflows(): iterable;

    /**
     * Create the item for an entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   Current entity.
     *
     * @return Item
     */
    public function createItem(EntityId $entityId, $entity): Item;
}
