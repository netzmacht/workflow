<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Manager;

use Netzmacht\Workflow\Data\EntityId;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;

/**
 * Workflow manager decorator caching the items and the relation between workflows and entities.
 *
 * @package Netzmacht\Workflow\Manager
 */
class CachedManager implements Manager
{
    /**
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
    public function handle(Item $item, $transitionName = null)
    {
        return $this->manager->handle($item, $transitionName);
    }

    /**
     * {@inheritdoc}
     */
    public function addWorkflow(Workflow $workflow)
    {
        $this->manager->addWorkflow($workflow);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflow(EntityId $entityId, $entity)
    {
        $key = $entityId->__toString();

        if (!isset($this->workflows[$key])) {
            $this->workflows[$key] = $this->manager->getWorkflow($entityId, $entity);
        }

        return $this->workflows[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByName($name)
    {
        return $this->manager->getWorkflowByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflowByItem(Item $item)
    {
        return $this->getWorkflow($item->getEntityId(), $item->getEntity());
    }

    /**
     * {@inheritdoc}
     */
    public function hasWorkflow(EntityId $entityId, $entity)
    {
        $key = $entityId->__toString();

        if (isset($this->workflows[$key])) {
            return true;
        }

        return $this->manager->hasWorkflow($entityId, $entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkflows()
    {
        return $this->manager->getWorkflows();
    }

    /**
     * {@inheritdoc}
     */
    public function createItem(EntityId $entityId, $entity)
    {
        $key = $entityId->__toString();

        if (!isset($this->items[$key])) {
            $this->items[$key] = $this->manager->createItem($entityId, $entity);
        }

        return $this->items[$key];
    }
}
