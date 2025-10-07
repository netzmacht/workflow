<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Override;

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

    #[Override]
    public function handle(
        Item $item,
        string|null $transitionName = null,
        bool $changeWorkflow = false,
    ): TransitionHandler|null {
        return $this->manager->handle($item, $transitionName);
    }

    #[Override]
    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->manager->addWorkflow($workflow);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function getWorkflow(EntityId $entityId, $entity): Workflow
    {
        $key = (string) $entityId;

        if (! isset($this->workflows[$key])) {
            $this->workflows[$key] = $this->manager->getWorkflow($entityId, $entity);
        }

        return $this->workflows[$key];
    }

    #[Override]
    public function getWorkflowByName(string $name): Workflow
    {
        return $this->manager->getWorkflowByName($name);
    }

    #[Override]
    public function getWorkflowByItem(Item $item): Workflow
    {
        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
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
    #[Override]
    public function getWorkflows(): iterable
    {
        return $this->manager->getWorkflows();
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    public function createItem(EntityId $entityId, $entity): Item
    {
        $key = (string) $entityId;

        if (! isset($this->items[$key])) {
            $this->items[$key] = $this->manager->createItem($entityId, $entity);
        }

        return $this->items[$key];
    }
}
