<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;

/**
 * Workflow manager decorator caching the items and the relation between workflows and entities.
 */
class CachedManager implements Manager
{
    /**
     * Workflow manager.
     */
    private Manager $manager;

    /**
     * Workflow entity mapping.
     *
     * @var array<string, Workflow>
     */
    private array $workflows = [];

    /**
     * Cached workflow items.
     *
     * @var array<string, Item>
     */
    private array $items = [];

    /**
     * Construct.
     *
     * @param Manager $manager The inside workflow manager.
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(
        Item $item,
        string|null $transitionName = null,
        bool $changeWorkflow = false,
    ): TransitionHandler|null {
        return $this->manager->handle($item, $transitionName);
    }

    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->manager->addWorkflow($workflow);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflow(EntityId $entityId, $entity): Workflow
    {
        $key = (string) $entityId;

        if (! isset($this->workflows[$key])) {
            $this->workflows[$key] = $this->manager->getWorkflow($entityId, $entity);
        }

        return $this->workflows[$key];
    }

    public function getWorkflowByName(string $name): Workflow
    {
        return $this->manager->getWorkflowByName($name);
    }

    public function getWorkflowByItem(Item $item): Workflow
    {
        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /**
     * {@inheritDoc}
     */
    public function hasWorkflow(EntityId $entityId, $entity): bool
    {
        $key = (string) $entityId;

        if (isset($this->workflows[$key])) {
            return true;
        }

        return $this->manager->hasWorkflow($entityId, $entity);
    }

    /**
     * {@inheritDoc}
     */
    public function getWorkflows(): iterable
    {
        return $this->manager->getWorkflows();
    }

    /**
     * {@inheritDoc}
     */
    public function createItem(EntityId $entityId, $entity): Item
    {
        $key = (string) $entityId;

        if (! isset($this->items[$key])) {
            $this->items[$key] = $this->manager->createItem($entityId, $entity);
        }

        return $this->items[$key];
    }
}
