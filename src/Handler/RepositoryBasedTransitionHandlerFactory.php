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

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Manager\WorkflowManager;
use Netzmacht\Workflow\Transaction\TransactionHandler;

/**
 * Class RepositoryBasedTransitionHandlerFactory creates a repository based transition handler.
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
     * @param string|null     $transitionName  Transition name.
     * @param string          $providerName    Provider name.
     * @param StateRepository $stateRepository The state repository.
     * @param WorkflowManager $workflowManager The workflow manager.
     *
     * @return TransitionHandler
     * @throws \Netzmacht\Workflow\Exception\WorkflowException
     */
    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        ?string $transitionName,
        string $providerName,
        StateRepository $stateRepository,
        WorkflowManager $workflowManager
    ): TransitionHandler {
        $repositoryBasedTransitionHandler = new RepositoryBasedTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $this->entityManager->getRepository($providerName),
            $stateRepository,
            $this->transactionHandler,
            $workflowManager
        );
        $changeWorkflowTransitionHandler = new ChangeWorkflowTransitionHandler($workflowManager, $repositoryBasedTransitionHandler);
        return $changeWorkflowTransitionHandler;
    }

    /**
     * Get the entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * Get the transaction handler.
     *
     * @return TransactionHandler
     */
    public function getTransactionHandler(): TransactionHandler
    {
        return $this->transactionHandler;
    }
}
