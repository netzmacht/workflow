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
    public function isSatisfiedBy($entity): bool;
}
