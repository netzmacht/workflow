<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Data;


interface EntityRepository 
{
    /**
     * Find an entity by id.
     *
     * @param int $entityId Entity id.
     *
     * @return Entity
     */
    public function find($entityId);

    /**
     * Add an entity to the repository.
     *
     * @param Entity $entity The new entity.
     *
     * @return void
     */
    public function add(Entity $entity);
}
