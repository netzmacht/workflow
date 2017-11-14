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
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\TransitionHandler;

/**
 * Workflow manager decorator caching the items and the relation between workflows and entities.
 *
 * @package Netzmacht\Workflow\Manager
 */
class CachedManager implements Manager
{
    /**
     * Workflow manager.
     *
     * @var Manager
     */
    private $manager;

    /**
     * Workflow entity mapping.
     *
     * @var array
     */
    private $workflows = array();

    /**
     * Cached workflow items.
     *
     * @var array
     */
    private $items = array();

    /**
     * Construct.
     *
     * @param Manager $manager The inside workflow manager.
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Item $item, ?string $transitionName = null): ?TransitionHandler
    {
        return $this->manager->handle($item, $transitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function addWorkflow(Workflow $workflow): Manager
    {
        $this->manager->addWorkflow($workflow);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(EntityId $entityId, $entity): Workflow
    {
        $key = (string) $entityId;

        if (!isset($this->workflows[$key])) {
            $this->workflows[$key] = $this->manager->getWorkflow($entityId, $entity);
        }

        return $this->workflows[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByName(string $name): Workflow
    {
        return $this->manager->getWorkflowByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByItem(Item $item): Workflow
    {
        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getWorkflows(): iterable
    {
        return $this->manager->getWorkflows();
    }

    /**
     * {@inheritdoc}
     */
    public function createItem(EntityId $entityId, $entity): Item
    {
        $key = (string) $entityId;

        if (!isset($this->items[$key])) {
            $this->items[$key] = $this->manager->createItem($entityId, $entity);
        }

        return $this->items[$key];
    }
}
