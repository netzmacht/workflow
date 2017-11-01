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

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
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
            $this->transactionHandler
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
     * Get the transaction handler.
     *
     * @return TransactionHandler
     */
    public function getTransactionHandler()
    {
        return $this->transactionHandler;
    }
}
