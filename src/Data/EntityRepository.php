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
     * @param mixed $entityId The Entity id.
     *
     * @return mixed
     */
    public function find($entityId);

    /**
     * Find multiple entities by a specification.
     *
     * @param Specification $specification The specification.
     *
     * @return iterable
     */
    public function findBySpecification(Specification $specification): iterable;

    /**
     * Add an entity to the repository.
     *
     * @param mixed $entity The new entity.
     *
     * @return void
     */
    public function add($entity): void;

    /**
     * Remove an entity from the repository.
     *
     * @param mixed $entity The entity.
     *
     * @return void
     */
    public function remove($entity): void;
}
