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

namespace Netzmacht\Workflow\Factory;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Handler\Listener;
use Netzmacht\Workflow\Handler\Listener\NoOpListener;
use Netzmacht\Workflow\Handler\RepositoryBasedTransitionHandler;
use Netzmacht\Workflow\Handler\TransitionHandler;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * Class EventDispatchingTransitionHandlerFactory creates the event dispatching transition handler.
 *
 * @package Netzmacht\Workflow\Factory
 */
class RepositoryBasedTransitionHandlerFactory implements TransitionHandlerFactory
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
     * @var Listener
     */
    private $listener;

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
     */
    public function __construct(
        EntityManager $entityManager,
        TransactionHandler $transactionHandler
    ) {
        $this->transactionHandler = $transactionHandler;
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
        return new RepositoryBasedTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $this->entityManager->getRepository($providerName),
            $stateRepository,
            $this->transactionHandler,
            $this->getListener()
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
     * Set the dispatcher which should be used.
     *
     * @param Listener $dispatcher The transition handler dispatcher.
     *
     * @return $this
     */
    public function setListener(Listener $dispatcher)
    {
        $this->listener = $dispatcher;

        return $this;
    }

    /**
     * Get the event dispatcher.
     *
     * @return Listener
     */
    public function getListener()
    {
        if (!$this->listener) {
            $this->listener = new NoOpListener();
        }

        return $this->listener;
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
