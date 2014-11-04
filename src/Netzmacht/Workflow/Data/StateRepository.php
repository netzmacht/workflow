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

use Netzmacht\Workflow\Flow\State;

/**
 * Interface StateRepository stores workflow states.
 *
 * @package Netzmacht\Workflow\Model
 */
interface StateRepository
{
    /**
     * Find last worfklow state of an entity.
     *
     * @param EntityId $entityId   The entity id.
     *
     * @return State[]
     */
    public function find(EntityId $entityId);

    /**
     * Add a new state.
     *
     * @param State $state The new state.
     *
     * @return void
     */
    public function add(State $state);
}
