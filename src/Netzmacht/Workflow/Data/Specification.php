<?php

/**
 * @package    workflow
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Data;

/**
 * Data specification.
 *
 * @package Netzmacht\Workflow\Data
 */
interface Specification
{
    /**
     * Check if an entity matches the specification.
     *
     * @param mixed $entity The entity.
     *
     * @return bool
     */
    public function isSatisfiedBy($entity);
}
