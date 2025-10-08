<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Netzmacht\Workflow\Data\EntityManager;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Override;

/**
 * Class RepositoryBasedTransitionHandlerFactory creates a repository based transition handler.
 */
final class RepositoryBasedTransitionHandlerFactory implements TransitionHandlerFactory
{
    /**
     * Transaction handler being used during workflow transitions.
     */
    private TransactionHandler $transactionHandler;

    /**
     * The entity manager.
     */
    private EntityManager $entityManager;

    /**
     * Construct.
     *
     * @param EntityManager      $entityManager      The entity manager.
     * @param TransactionHandler $transactionHandler Transaction handler being used during workflow transitions.
     */
    public function __construct(
        EntityManager $entityManager,
        TransactionHandler $transactionHandler,
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
     */
    #[Override]
    public function createTransitionHandler(
        Item $item,
        Workflow $workflow,
        string|null $transitionName,
        string $providerName,
        StateRepository $stateRepository,
    ): TransitionHandler {
        return new RepositoryBasedTransitionHandler(
            $item,
            $workflow,
            $transitionName,
            $this->entityManager->getRepository($providerName),
            $stateRepository,
            $this->transactionHandler,
        );
    }

    /**
     * Get the entity manager.
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * Get the transaction handler.
     */
    public function getTransactionHandler(): TransactionHandler
    {
        return $this->transactionHandler;
    }
}
