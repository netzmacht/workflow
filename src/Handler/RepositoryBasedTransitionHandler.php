<?php

declare(strict_types=1);

namespace Netzmacht\Workflow\Handler;

use Exception;
use Netzmacht\Workflow\Data\EntityRepository;
use Netzmacht\Workflow\Data\StateRepository;
use Netzmacht\Workflow\Exception\WorkflowException;
use Netzmacht\Workflow\Flow\Item;
use Netzmacht\Workflow\Flow\State;
use Netzmacht\Workflow\Flow\Workflow;
use Netzmacht\Workflow\Transaction\TransactionHandler;
use Override;
use Throwable;

/**
 * Class RepositoryBasedTransitionHandler handles the transition to another step in the workflow.
 *
 * It uses an collection repository approach to store entities.
 */
class RepositoryBasedTransitionHandler extends AbstractTransitionHandler
{
    /**
     * The entity repository.
     */
    private EntityRepository $entityRepository;

    /**
     * The state repository.
     */
    private StateRepository $stateRepository;

    /**
     * Construct.
     *
     * @param Item               $item               The item.
     * @param Workflow           $workflow           The current workflow.
     * @param string|null        $transitionName     The transition to be handled.
     * @param EntityRepository   $entityRepository   EntityRepository which stores changes.
     * @param StateRepository    $stateRepository    StateRepository which stores new states.
     * @param TransactionHandler $transactionHandler TransactionHandler take care of transactions.
     *
     * @throws WorkflowException If invalid transition name is given.
     */
    public function __construct(
        Item $item,
        Workflow $workflow,
        string|null $transitionName = null,
        EntityRepository $entityRepository,
        StateRepository $stateRepository,
        TransactionHandler $transactionHandler,
    ) {
        parent::__construct($item, $workflow, $transitionName, $transactionHandler);

        $this->entityRepository = $entityRepository;
        $this->stateRepository  = $stateRepository;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception If something went wrong during action execution.
     */
    #[Override]
    public function transit(): State
    {
        $this->guardValidated();

        $this->transactionHandler->begin();

        try {
            $state = $this->executeTransition();

            foreach ($this->getItem()->releaseRecordedStateChanges() as $state) {
                $this->stateRepository->add($state);
            }

            $this->entityRepository->add($this->getItem()->getEntity());
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();

            throw $e;
        }

        $this->transactionHandler->commit();

        return $state;
    }
}
