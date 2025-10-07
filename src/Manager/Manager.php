<?php

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
 */
interface Manager
{
    /**
     * Handle a workflow transition of an entity will createRepository a transition handler.
     *
     * If no matching workflow definition is found null will be returned.
     *
     * @param Item        $item           The current workflow item.
     * @param string|null $transitionName Transition name, required if the workflow has already started.
     * @param bool        $changeWorkflow If true, the item is detached from the current workflow if another workflow is
     *                                    used.
     *
     * @throws WorkflowException If something went wrong.
     */
    public function handle(
        Item $item,
        string|null $transitionName = null,
        bool $changeWorkflow = false,
    ): TransitionHandler|null;

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
     * @throws WorkflowNotFound When no workflow is found.
     */
    public function getWorkflow(EntityId $entityId, mixed $entity): Workflow;

    /**
     * Get Workflow by its name.
     *
     * @param string $name Name of workflow.
     *
     * @throws WorkflowNotFound When no workflow is found.
     */
    public function getWorkflowByName(string $name): Workflow;

    /**
     * Get workflow by item.
     *
     * @param Item $item Workflow item.
     *
     * @throws WorkflowNotFound When no workflow is found.
     */
    public function getWorkflowByItem(Item $item): Workflow;

    /**
     * Consider if entity has an workflow.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     */
    public function hasWorkflow(EntityId $entityId, mixed $entity): bool;

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
     */
    public function createItem(EntityId $entityId, mixed $entity): Item;
}
