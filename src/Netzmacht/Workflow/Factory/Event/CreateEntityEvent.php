<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Factory\Event;

use Netzmacht\Workflow\Data\Entity;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateEntityEvent is dispatched when a new entity is created.
 *
 * @package Netzmacht\Contao\Workflow\Factory\Event
 */
class CreateEntityEvent extends Event
{
    const NAME = 'workflow.factory.create-entity';

    /**
     * The entity being created.
     *
     * @var Entity
     */
    private $entity;

    /**
     * The given data model.
     *
     * @var mixed
     */
    private $model;

    /**
     * Optional provider name. Usually database table name.
     *
     * @var string|null
     */
    private $provider;

    /**
     * Construct.
     *
     * @param mixed       $model    The data model.
     * @param string|null $provider The $provider name.
     */
    public function __construct($model, $provider = null)
    {
        $this->model    = $model;
        $this->provider = $provider;
    }

    /**
     * Get the data model.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the table name.
     *
     * @return null|string
     */
    public function getProviderName()
    {
        return $this->provider;
    }

    /**
     * Get the created entity.
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the created entity.
     *
     * @param Entity $entity The created entity.
     *
     * @return $this
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;

        return $this;
    }
}
