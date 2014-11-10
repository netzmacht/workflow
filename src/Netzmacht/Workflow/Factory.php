<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow;

use Netzmacht\Workflow\Data\Entity;
use Netzmacht\Workflow\Factory\Event\CreateEntityEvent;
use Netzmacht\Workflow\Factory\Event\CreateFormEvent;
use Netzmacht\Workflow\Factory\Event\CreateManagerEvent;
use Netzmacht\Workflow\Factory\Event\CreateUserEvent;
use Netzmacht\Workflow\Form\Form;
use Netzmacht\Workflow\Security\User;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class Factory dispatches events which the implementation can subscribe to create the instances.
 *
 * @package Netzmacht\Workflow
 */
class Factory
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Construct.
     *
     * @param EventDispatcher $eventDispatcher The event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create a workflow manager.
     *
     * @param string      $providerName The provider name. Typically a database table name.
     * @param string|null $type         Optional workflow type limitation. Is passed to the factory event.
     *
     * @return Manager
     *
     * @throws RuntimeException If manager was not created.
     */
    public function createManager($providerName, $type = null)
    {
        $event = new CreateManagerEvent($providerName, $type);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        if (!$event->getManager()) {
            throw new RuntimeException(
                sprintf('No workflow manager were created during dispatching event "%s"', $event::NAME)
            );
        }

        return $event->getManager();
    }


    /**
     * Create a new entity for a model.
     *
     * @param mixed       $model Create an workflow entity.
     * @param string|null $table Table name is required for Contao results or array rows.
     *
     * @throws RuntimeException If no entity could be created.
     *
     * @return Entity
     */
    public function createEntity($model, $table = null)
    {
        $event = new CreateEntityEvent($model, $table);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        if (!$event->getEntity()) {
            throw new RuntimeException(
                sprintf('No entity were created during dispatching event "%s"', $event::NAME)
            );
        }

        return $event->getEntity();
    }

    /**
     * Create a form.
     *
     * @param string $type The form type.
     *
     * @return Form
     *
     * @throws RuntimeException If form was not created.
     */
    public function createForm($type)
    {
        $event = new CreateFormEvent($type);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        if (!$event->getForm()) {
            throw new RuntimeException(sprintf('Could not create form type "%s"', $type));
        }

        return $event->getForm();
    }

    /**
     * Create user instance.
     *
     * @return User
     */
    public function createUser()
    {
        $user  = new User();
        $event = new CreateUserEvent($user);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getUser();
    }
}
