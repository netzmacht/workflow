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

use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Flow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;

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
     * Handle a workflow transition of an entity will createRepository a transition handler.
     *
     * If no matching workflow definition is found false will be returned.
     *
     * @param Item   $item           The current workflow item.
     * @param string $transitionName Transition name, required if workflow has already started.
     *
     * @throws WorkflowException If something went wrong.
     *
     * @return bool|TransitionHandler
     */
    public function handle(Item $item, $transitionName = null);

    /**
     * Add a workflow to the manager.
     *
     * @param Workflow $workflow The workflow being added.
     *
     * @return Manager
     */
    public function addWorkflow(Workflow $workflow): self;

    /**
     * Get a workflow for the given entity.
     *
     * @param EntityId $entityId The entity id.
     * @param mixed    $entity   The entity.
     *
     * @return Workflow|bool
     */
    public function getWorkflow(EntityId $entityId, $entity);

    /**
     * Get Workflow by its name.
     *
     * @param string $name Name of workflow.
     *
     * @return bool|Workflow
     */
    public function getWorkflowByName(string $name);

    /**
     * Get workflow by item.
     *
     * @param Item $item Workflow item.
     *
     * @return bool|Workflow
     */
    public function getWorkflowByItem(Item $item);

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
