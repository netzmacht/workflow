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
     * @param EntityId $entityId The entity id.
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
    public function add(State $state): void;
}
