<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Data;

/**
 * Interface EntityRepository describes the repository which stores the items.
 *
 * @package Netzmacht\Workflow\Data
 */
interface EntityRepository
{
    /**
     * Find an entity by id.
     *
     * @param int $entityId The Entity id.
     *
     * @return mixed
     */
    public function find($entityId);

    /**
     * Add an entity to the repository.
     *
     * @param mixed $entity The new entity.
     *
     * @return void
     */
    public function add($entity);
}
