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

namespace Netzmacht\Workflow\Data;

/**
 * Interface EntityManager create entity repositories.
 *
 * @package Netzmacht\Workflow\Data
 */
interface EntityManager
{
    /**
     * Create an entity repository.
     *
     * @param string $providerName The provider name.
     *
     * @throws \InvalidArgumentException If repository could not be created.
     *
     * @return EntityRepository
     */
    public function getRepository($providerName);
}
