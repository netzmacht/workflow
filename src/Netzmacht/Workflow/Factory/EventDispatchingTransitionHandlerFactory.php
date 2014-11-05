<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Workflow\Factory;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\EventDispatchingTransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Class EventDispatchingTransitionHandlerFactory creates the event dispatching transition handler.
 *
 * @package Netzmacht\Workflow\Factory
 */
class EventDispatchingTransitionHandlerFactory implements TransitionHandlerFactory
{
    /**
     * Transaction handler being used during workflow transitions.
     *
     * @var TransactionHandler
     */
    private $transactionHandler;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Construct.
     *
     * @param EntityManager      $entityManager      The entity manager.
     * @param TransactionHandler $transactionHandler Transaction handler being used during workflow transitions.
     * @param EventDispatcher    $eventDispatcher    The event dispatcher.
     */
    function __construct(
        EntityManager $entityManager,
        TransactionHandler $transactionHandler,
        EventDispatcher $eventDispatcher
    )
    {
        $this->transactionHandler = $transactionHandler;
        $this->eventDispatcher    = $eventDispatcher;
        $this->entityManager      = $entityManager;
    }

    /**
     * Create a transition handler.
     *
     * @param Item            $item            Workflow item.
     * @param Workflow        $workflow        Workflow definition.
     * @param string          $transitionName  Transition name.
     * @param string          $providerName    Provider name.
     * @param StateRepository $stateRepository The state repository.
     *
     * @return TransitionHandler
     */
    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        $transitionName,
        $providerName,
        StateRepository $stateRepository
    ) {
        return new EventDispatchingTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $this->entityManager->getRepository($providerName),
            $stateRepository,
            $this->transactionHandler,
            $this->eventDispatcher
        );
    }

    /**
     * Get the entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Get the event dispatcher.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Get the transaction handler.
     *
     * @return TransactionHandler
     */
    public function getTransactionHandler()
    {
        return $this->transactionHandler;
    }
}
